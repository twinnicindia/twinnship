<?php

namespace App\Http\Controllers;

use App\Models\Admin_rights;
use App\Models\Brands;
use App\Models\Channel_partners;
use App\Models\Logistics;
use App\Models\Slider;
use App\Models\Testimonial;
use App\Models\WebContactUs;
use Illuminate\Http\Request;
class ContactController extends Controller
{
    function __construct()
    {

    }
    function index(){
        $data['menus']=Admin_rights::get_menus(Session()->get('MyAdmin')->id);
        $data['contact'] = WebContactUs::orderBy('id', 'desc')->get();
        return view('admin.contact',$data);
    }
}
