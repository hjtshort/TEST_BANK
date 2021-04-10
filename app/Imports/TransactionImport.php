<?php


namespace App\Imports;


use App\Models\FileImport;
use App\Models\Transaction;
use App\Models\TransactionFail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Carbon;

use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;

class TransactionImport implements WithHeadingRow, WithChunkReading, toModel, WithValidation, SkipsOnFailure, ShouldQueue
{

    use Importable;

    protected $fileImport;
    protected $isUpdated = false;

    public function __construct(FileImport $fileImport)
    {
        $this->fileImport = $fileImport;
    }


    public function onFailure(Failure ...$failures)
    {
        $data = collect([]);
        foreach ($failures as $row) {
            $value = $row->values();
            $data[] = new TransactionFail([
                'date' => $value['date'] ?? null,
                'content' => $value['content'] ?? null,
                'amount' => $value['amount'] ?? null,
                'type' => $value['type'] ?? null,
                'file_import'
            ]);
        }

        $data
            ->chunk(
                $this->batchSize()
            )
            ->each(function ($item) {
                $this->fileImport->transactionFailed()->saveMany($item->values());
            });

        if ($data->count() > 0) {
            $this->fileImport->state = 2;
            if (!$this->isUpdated) {
                $this->fileImport->save();
                $this->isUpdated = true;
            }
        }
    }

    public function formatAmount(string $amount): int
    {
        return (int)str_replace('.', '', $amount);
    }

    public function model(array $row): Transaction
    {
        return new Transaction([
            'date' => Carbon::createFromFormat('d/m/Y H:i:s', $row['date'])->toDateTimeString(),
            'content' => $row['content'],
            'amount' => $this->formatAmount($row['amount']),
            'type' => $row['type'],
        ]);
    }

    public function rules(): array
    {
        return [
            'date' => 'required|date_format:d/m/Y H:i:s',
            'content' => 'required',
            'amount' => 'required',
            'type' => function ($attribute, $value, $onFailure) {
                if (!in_array(Str::lower($value), ['withdraw', 'deposit'])) {
                    $onFailure();
                }
            }
        ];
    }

    public function batchSize(): int
    {
        return 500;
    }

    public function chunkSize(): int
    {
        return 10000;
    }
}