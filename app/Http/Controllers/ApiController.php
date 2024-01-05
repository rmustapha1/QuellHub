<?php
//namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiController extends Controller
{
  // Api - Hostels List
  public function hostels()
  {
    // $pageConfigs = ['pageHeader' => false];

    // return view('/content/dashboard/dashboard-analytics', ['pageConfigs' => $pageConfigs]);
    $data = [['name' => 'hostel1', 'test' => 'fldsjl'],['name' => 'hostel1', 'test' => 'fldsjl']];
    return $data;
  }

  // Dashboard - Ecommerce
  // public function dashboardEcommerce()
  // {
  //   $pageConfigs = ['pageHeader' => false];

  //   return view('/content/dashboard/dashboard-ecommerce', ['pageConfigs' => $pageConfigs]);
  // }
}
