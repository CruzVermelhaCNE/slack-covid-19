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
            $data = Intel::where('country', $element->country)->where('state', $element->state)->orderBy('created_at', 'desc')->limit(2)->get();
            $infected = null;
            $delta_confirmed = null;
            $delta_infected = null;
            $delta_recovered = null;
            $delta_deaths = null;
            if ($data->count == 2) {
                $infected = [
                0 => $data[0]->confirmed - $data[0]->recovered - $data[0]->deaths,
                1 => $data[1]->confirmed - $data[1]->recovered - $data[1]->deaths
            ];
                $delta_confirmed = $data[0]->confirmed - $data[1]->confirmed;
                $delta_infected = $infected[0] - $infected[1];
                $delta_recovered = $data[0]->recovered - $data[1]->recovered;
                $delta_deaths = $data[0]->deaths - $data[1]->deaths;
            } else {
                $infected =  $data[0]->confirmed - $data[0]->recovered - $data[0]->deaths;
                $delta_confirmed = $data[0]->confirmed;
                $delta_infected = $infected[0];
                $delta_recovered = $data[0]->recovered;
                $delta_deaths = $data[0]->deaths;
            }

            if ($delta_confirmed != 0 || $delta_infected != 0 || $delta_recovered != 0 || $delta_deaths != 0) {
                $confirmed_text = "";
                $infected_text = "";
                $recovered_text = "";
                $deaths_text = "";
                if ($delta_confirmed > 0) {
                    $delta_confirmed = "+".$delta_confirmed;
                } elseif ($delta_confirmed == 0) {
                    $delta_confirmed = "=";
                }
                $confirmed_text = $data[0]->confirmed . " (".$delta_confirmed.")";
                if ($delta_infected > 0) {
                    $delta_infected = "+".$delta_infected;
                } elseif ($delta_infected == 0) {
                    $delta_infected = "=";
                }
                $infected_text .= $infected[0] . " (".$delta_infected.")";
                if ($delta_recovered > 0) {
                    $delta_recovered = "+".$delta_recovered;
                } elseif ($delta_recovered == 0) {
                    $delta_recovered = "=";
                }
                $recovered_text = $data[0]->recovered . " (".$delta_recovered.")";
                if ($delta_deaths > 0) {
                    $delta_deaths = "+".$delta_deaths;
                } elseif ($delta_recovered == 0) {
                    $delta_recovered = "=";
                }
                $deaths_text = $data[0]->deaths . " (".$delta_deaths.")";
                $data[0]->notify(new InfectionUpdateToSlack($element->country, $element->state, $confirmed_text, $infected_text, $recovered_text, $deaths_text));
            }
        }
    }
}
