<?php

namespace App\Imports;

use App\Models\Produto;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Str;

class ProdutosImport extends Command implements ToModel, WithStartRow, WithBatchInserts, WithChunkReading
{
    /**
     *
     * @param array $row
     *
     * @return Produto|null
     */
    public function model(array $row)
    {
        if (empty($row[0])) {
            return null;
        }

        return new Produto([
            'id_publico' => (string) Str::uuid(),
            'id_categoria' => 1,
            'codigo' => $row[0],
            'nome' => $row[1],
            'descricao' => "", // TODO: encontrar alguma maneira de popular esse campo.
            'preco' => $row[10],
            'valor_custo' => $row[9],
            'valor_venda' => $row[10],
            'qtd_estoque' => $row[5],
            'qtd_minima' => $row[6]
        ]);
    }

    // TODO: Encontrar alguma forma de fazer esse método "saber"
    // em que linha começa o heading row.
    public function startRow(): int
    {
        return 8;
    }

    public function batchSize(): int
    {
        return 300;
    }

    public function chunkSize(): int
    {
        return 300;
    }


    // TODO: row validation
}
