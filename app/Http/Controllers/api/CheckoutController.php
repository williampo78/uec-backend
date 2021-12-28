<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\UniversalService;

class CheckoutController extends Controller
{

    private $apiIndexService;

    public function __construct(UniversalService $universalService)
    {
        $this->universalService = $universalService;
    }

    /*
     * 取得發票捐贈機構
     *
     */
    public function getDonatedInstitution()
    {
        $institutin = $this->universalService->getLookupValues('DONATED_INSTITUTION');
        $data = [];
        foreach ($institutin as $code => $value) {
            $data[] = array(
                "code" => $code,
                "description" => $value
            );
        }
        return response()->json(['status' => true, 'error_code' => null, 'error_msg' => null, 'result' => $data]);

    }
}
