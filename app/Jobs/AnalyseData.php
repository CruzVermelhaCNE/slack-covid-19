<?php

namespace App\Jobs;

use App\Intel;
use App\Notifications\InfectionUpdateToSlack;
use App\SlackChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AnalyseData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $region = Intel::select(["country","state"])->groupBy(["country","state"])->get();
        foreach ($region as $element) {
            $data = Intel::where('country', $element->country)->where('state',$element->state)->orderBy('created_at','desc')->limit(2)->get();
            $delta_confirmed = $data[0]->confirmed - $data[1]->confirmed;
            $delta_recovered = $data[0]->recovered - $data[0]->recovered;
            $delta_deaths = $data[0]->deaths - $data[0]->deaths;
            $text = "";
            $send = false;
            if($element->state) {
                $text = $element->state . ", " . $element->country;
            }
            else {
                $text = $element->country;
            }
            $text .= ": ";
            if($delta_confirmed != 0) {
                if($delta_confirmed > 0) {
                    $delta_confirmed = "+".$delta_confirmed;
                }
                $text .= "Confirmed: ".$delta_confirmed . ", ";
                $send = true;
            }
            if($delta_recovered != 0) {
                if($delta_recovered > 0) {
                    $delta_recovered = "+".$delta_recovered;
                }
                $text .= "Recovered: ".$delta_recovered. ", ";
                $send = true;
            } 
            if($delta_deaths != 0) {
                if($delta_deaths > 0) {
                    $delta_deaths = "+".$delta_deaths;
                }
                $text .= "Deaths: ".$delta_deaths. ", ";
                $send = true;
            }
            if($send) {
                $infected = $data[0]->confirmed - $data[0]->recovered - $data[0]->deaths;
                $text .= "Remaining Infected: ". $infected;
                $text .= " (Confirmed: ".$data[0]->confirmed.", Recovered: ".$data[0]->recovered.", Deaths: ".$data[0]->deaths.")";             
                $data[0]->notify(new InfectionUpdateToSlack($text));
            }
        }

    }
}
