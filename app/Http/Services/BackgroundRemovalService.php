<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Http\UploadedFile;

class BackgroundRemovalService
{
    public function removeBackground($imagePath)
    {
        // Log the process to help with debugging
        Log::info('Starting background removal', ['image_path' => $imagePath]);

        // Handle UploadedFile object
        if ($imagePath instanceof UploadedFile) {
            // Get the temp path directly
            $absoluteInputPath = $imagePath->getRealPath();
            Log::info('Got real path from uploaded file', ['temp_path' => $absoluteInputPath]);

            if (!file_exists($absoluteInputPath)) {
                throw new Exception("Temporary file does not exist: {$absoluteInputPath}");
            }

            // Create output filename based on original filename
            $fileBasename = pathinfo($imagePath->getClientOriginalName(), PATHINFO_FILENAME);
            $uniqueId = uniqid();
            $filename = "{$fileBasename}_{$uniqueId}";
        }
        // Handle string path
        else {
            $storagePath = storage_path('app/');
            $absoluteInputPath = $storagePath . $imagePath;
            $filename = pathinfo($imagePath, PATHINFO_FILENAME);
        }

        Log::info('Absolute input path', ['absolute_input_path' => $absoluteInputPath]);

        // Create output directory if it doesn't exist
        $outputDir = storage_path('app/nobg/');
        if (!file_exists($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        // Create output path
        $outputFilename = $filename . '_nobg.png';
        $outputPath = $outputDir . $outputFilename;
        Log::info('Output path', ['output_path' => $outputPath]);

        // Check if input file exists and is readable
        if (!file_exists($absoluteInputPath)) {
            throw new Exception("Input file does not exist: {$absoluteInputPath}");
        }

        if (!is_readable($absoluteInputPath)) {
            throw new Exception("Input file is not readable: {$absoluteInputPath}");
        }

        // Get the working directory for the Node script
        $nodeScriptDir = base_path('node-scripts');
        $scriptName = 'remove-background.js';

        // Get path to node binary (use the system node if not specified)
        $nodePath = exec('which node');
        $nodeBinary = env('BROWSESHOT_NODE_BINARY', $nodePath);

        // Remove any backslash escapes that might be in the env value
        $nodeBinary = str_replace('\\', '', $nodeBinary);

        Log::info('Using Node binary', ['path' => $nodeBinary]);

        // Build command with proper escaping
        $command = "cd " . escapeshellarg($nodeScriptDir) . " && " .
            escapeshellarg($nodeBinary) . " " .
            escapeshellarg($scriptName) . " " .
            escapeshellarg($absoluteInputPath) . " " .
            escapeshellarg($outputPath) . " 2>&1";

        Log::info('Executing command', ['command' => $command]);

        exec($command, $output, $returnCode);

        Log::info('Command result', [
            'return_code' => $returnCode,
            'output' => $output
        ]);

        if ($returnCode !== 0) {
            $errorMessage = implode("\n", $output);
            Log::error('Background removal failed', [
                'error_message' => $errorMessage,
                'return_code' => $returnCode
            ]);
            throw new Exception('Background removal failed: ' . $errorMessage);
        }

        return 'nobg/' . $outputFilename;
    }
}
