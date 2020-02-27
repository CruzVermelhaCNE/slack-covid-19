<?php

namespace App\Jobs;

use App\Intel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessJohnsHopkinsCSSE implements ShouldQueue
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
        $client = new \GuzzleHttp\Client();
        $res = $client->request('GET', "https://services1.arcgis.com/0MSEUqKaxRlEPj5g/ArcGIS/rest/services/ncov_cases/FeatureServer/1/query?where=OBJECTID+%3E+0&outFields=*&returnExceededLimitFeatures=true&f=json");
        $content = $res->getBody();
        $data = json_decode($content, true);
        foreach ($data["features"] as $feature) {
            $country = $feature["attributes"]["Country_Region"];
            $state = $feature["attributes"]["Province_State"];
            $confirmed = $feature["attributes"]["Confirmed"];
            $deaths = $feature["attributes"]["Deaths"];
            $recovered = $feature["attributes"]["Recovered"];
            $suspected = null;
            $source = 0;
            $data = ["country" => $country, "state" => $state, "confirmed" => $confirmed, "deaths" => $deaths, "recovered" => $recovered,"suspected" => $suspected, "source" => $source];
            Intel::create($data);
        }
        AnalyseData::dispatch();

    }
}
