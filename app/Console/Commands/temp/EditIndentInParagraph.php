<?php

namespace App\Console\Commands\temp;

use Illuminate\Console\Command;
use App\Models\Paragraph;

class EditIndentInParagraph extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'temp:edit_indent_in_paragraphs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Edit inden in paragraphs';

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
     * @return int
     */
    public function handle()
    {
        $paragraphs = Paragraph::get();

        foreach($paragraphs as $paragraph) {
            $c = $paragraph->content;

            $new_content = '';
            for($i = 0; $i<strlen($c); $i++) {
                if ($c[$i] == 's') {
                    $str = '';
                    for($j = 0; $j<27; $j++) {
                        if (isset($c[$i+$j])) {
                            $str .= $c[$i + $j];
                        }
                    }
                    if ($str == 'style="margin-left:2.5rem;"') {
                        $new_content .= 'style="margin-left:1.25rem;"';
                        $i = $i + 26;
                        continue;
                    }

                    $str = '';
                    for($j = 0; $j<25; $j++) {
                        if (isset($c[$i+$j])) {
                            $str .= $c[$i + $j];
                        }
                    }
                    if ($str == 'style="margin-left:5rem;"') {
                        $new_content .= 'style="margin-left:2.5rem;"';
                        $i = $i + 24;
                        continue;
                    }

                    $str = '';
                    for($j = 0; $j<27; $j++) {
                        if (isset($c[$i+$j])) {
                            $str .= $c[$i + $j];
                        }
                    }
                    if ($str == 'style="margin-left:7.5rem;"') {
                        $new_content .= 'style="margin-left:3.75rem;"';
                        $i = $i + 26;
                        continue;
                    }
                }
                $new_content .= $c[$i];
            }

            $paragraph->content = $new_content;
            $paragraph->save();
        }
        return 0;
    }
}
