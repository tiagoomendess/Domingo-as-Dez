<?php

namespace App\Console\Commands;

use App\Article;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GptArticles extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gpt:articles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extracts the articles';

    protected $fileType = null;

    protected $fileTypes = ['csv', 'json', 'pdf'];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        do {
            $this->fileType = $this->ask('File type ' . implode(', ', $this->fileTypes) . '?');
            if (!in_array($this->fileType, $this->fileTypes)) {
                $this->error('Invalid file type "' . $this->fileType . '" provided. Please try again.');
            }

        } while (!in_array($this->fileType, $this->fileTypes));

        $article_id = $this->ask('What is the article id? 0 for multiple articles');
        if ($article_id > 0) {
            $this->handleSingleArticle($article_id);
        } else {
            $this->handleMultipleArticles();
        }

        $this->info('Finished generating');
    }

    private function handleMultipleArticles() {

        $start_date = $this->getDate('start');
        $end_date = $this->getDate('end');

        $this->info("Generating articles from " . $start_date->format('Y-m-d') . " to " . $end_date->format('Y-m-d') . "...");

        foreach (Article::where('date', '>=', $start_date)->where('date', '<=', $end_date)->where('visible', true)->cursor() as $article) {
            $date = Carbon::createFromFormat('Y-m-d H:i:s', $article->date)->format('Y-m-d');
            $this->generateArticle($article);
            $this->info("Article $article->id - $date - generated.");
        }
    }

    private function getDate($date_type): Carbon {
        $time = $date_type == 'start' ? '00:00:00' : '23:59:59';
        do {
            $date_str = $this->ask("What is the $date_type date? (ex: 2023-10-18)");
            try {
                $date = Carbon::createFromFormat('Y-m-d H:i:s', "$date_str $time");
            } catch (\Exception $e) {
                $date = null;
                $this->error("Invalid date '$date_str' provided. Please try again.");
                continue;
            }

        } while (!$date);

        return $date;
    }

    private function handleSingleArticle($article_id)
    {
        $article = Article::where('id', $article_id)->first();
        if (empty($article)) {
            $this->error("Article $article_id was not found");
            return;
        }
        $this->generateArticle($article);
    }

    private function generateArticle($article)
    {
        switch ($this->fileType) {
            case 'csv':
                $this->generateCsv($article);
                break;
            case 'json':
                $this->generateJson($article);
                break;
            case 'pdf':
                $this->generatePdf($article);
                break;
            default:
                $this->error("No implementation for file type " . $this->fileType);
        }
    }

    private function generatePdf($article) {
        $now = Carbon::now();
        $filename = "article_" . $article->id . "_" . $now->format("YmdHis") . ".pdf";
        $pdf = PDF::loadView('pdf.article', ['article' => $article]);

        Storage::disk('public')->put("exports/articles/pdf/$filename", $pdf->output());
    }

    private function generateJson($article) {
        $now = Carbon::now();
        $filename = "article_" . $article->id . "_" . $now->format("YmdHis") . ".json";
        $data = [
            'título' => $article->title,
            'descrição' => $article->description,
            'texto' => $article->text,
            'data' => Carbon::createFromFormat('Y-m-d H:i:s', $article->date)->format('Y-m-d'),
        ];

        Storage::disk('public')->put("exports/articles/json/$filename", json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    private function generateCsv($article) {
        $now = Carbon::now();
        $filename = "article_" . $article->id . "_" . $now->format("YmdHis") . ".csv";

        $data = ["título, descrição, texto, data"];
        $data[] = '"' . $article->title . '";"' . $article->description . '";"' . $article->text . '";"' . Carbon::createFromFormat('Y-m-d H:i:s', $article->date)->format('Y-m-d') . '"';

        Storage::disk('public')->put("exports/articles/csv/$filename", implode("\n", $data));
    }
}
