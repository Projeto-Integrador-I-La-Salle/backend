import { removeBackground } from '@imgly/background-removal-node';
import { existsSync, mkdirSync, writeFileSync } from 'fs';
import path, { dirname, resolve } from 'path';

// Helper function to convert file path to file:// URL
function pathToFileURL(filepath) {
    // Check if file exists first
    if (!existsSync(filepath)) {
        throw new Error(`Input file does not exist: ${filepath}`);
    }

    // Normalize the path and convert it to a file URL
    const resolvedPath = resolve(filepath);
    let fileURL = 'file://' + (resolvedPath.startsWith('/') ? '' : '/') + resolvedPath;

    // Ensure spaces are properly encoded
    return fileURL.replace(/\s/g, '%20');
}

async function main() {
    // Get command line arguments
    const inputPath = process.argv[2];
    const outputPath = process.argv[3];

    console.log(`Input file: ${inputPath}`);
    console.log(`Output file: ${outputPath}`);

    // Check if input file exists
    if (!existsSync(inputPath)) {
        console.error(`Error: Input file does not exist: ${inputPath}`);
        process.exit(1);
    }

    try {
        // Convert input path to proper file:// URI
        const inputFileUri = pathToFileURL(inputPath);
        console.log('Input file URI:', inputFileUri);

        // Get the project root directory (one level up from node-scripts)
        const __dirname = path.resolve();
        const projectRoot = resolve(__dirname, '..');

        // Set options with debug mode and correct path to resources
        const options = {
            publicPath: `file://${projectRoot}/node_modules/@imgly/background-removal-node/dist/`,
            debug: false,
            proxyToWorker: true,
            fetchArgs: {},
            model: 'medium',
            output: { format: 'image/webp', quality: 0.8 }
        };

        //console.log('Config:', JSON.stringify(options, null, 2));

        // Remove background with options
        console.log('Processing image...');
        console.log('Loading model...');

        const blob = await removeBackground(inputFileUri, options);

        // Convert the blob to a buffer
        console.log('Converting result to buffer...');
        const buffer = Buffer.from(await blob.arrayBuffer());

        // Create output directory if it doesn't exist
        const outputDir = dirname(outputPath);
        if (!existsSync(outputDir)) {
            mkdirSync(outputDir, { recursive: true });
        }

        // Save to output file
        console.log('Saving result to:', outputPath);
        writeFileSync(outputPath, buffer);
        console.log('Success!');
    } catch (error) {
        console.error('Error:', error);
        process.exit(1);
    }
}

main();

