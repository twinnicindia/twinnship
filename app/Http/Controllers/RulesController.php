<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Configuration;
use App\Http\Controllers\Utilities;
use App\Models\Partners;
use App\Models\Preferences;
use App\Models\Rules;
use App\Models\SKU;

class RulesController extends Controller
{
    protected $info, $utilities;
    public function __construct()
    {
        $this->info['config'] = Configuration::find(1);
        $this->utilities = new Utilities();
    }

    public function rules()
    {
        $data = $this->info;
        $data['partner'] = Partners::where('status', 'y')->get();
        $data['preferences'] = Preferences::where('seller_id', Session()->get('MySeller')->id)->get();
        $data['allPartners'] = Partners::getPartnerKeywordList();
        $cnt = 0;
        foreach ($data['preferences'] as $p) {
            $data['preferences'][$cnt++]['rules'] = Rules::where('preferences_id', $p->id)->get();
        }
        return view('seller.rules', $data);
    }

    public function add_rule(Request $request)
    {
        $isPrioritize = false;
        //    print_r($request->filter); exit;
        $data = array(
            'seller_id' => Session()->get('MySeller')->id,
            'rule_name' => $request->name,
            'priority' => $request->priority,
            'match_type' => $request->filter_type,
            'priority1' => $request->courier_priority_1,
            'priority2' => $request->courier_priority_2,
            'priority3' => $request->courier_priority_3,
            'priority4' => $request->courier_priority_4
        );

        $preference = Preferences::create($data);

        // $n = count($request->product_name);
        foreach ($request->filter as $r) {
            $matchValue = $r['field'] == "weight" ? (floatval($r['value']) * 1000) : $r['value'];
            $data_rules = array(
                'preferences_id' => $preference->id,
                'criteria' =>   $r['field'],
                'match_type' =>  $r['condition'],
                'match_value' => $matchValue
            );
            if($r['field'] == 'order_type')
                $isPrioritize = true;
            // dd($data_rules);
            Rules::create($data_rules);
        }
        if($isPrioritize){
            $preference->priority=1;
            $preference->save();
            Preferences::where('seller_id',Session()->get('MySeller')->id)->where('id','!=',$preference->id)->increment('priority',1);
        }
        // generating notification
        $this->utilities->generate_notification('Success', 'Rules added successfully', 'success');
        return back();
    }

    public function check_priority($value = 0)
    {
        if ($value != 0) {
            $priority = Preferences::where('priority', $value)->where('seller_id', Session()->get('MySeller')->id)->count();
            return $priority;
        } else
            return 0;
    }

    function rule_status(Request $request)
    {
        $data = array(
            'status' => $request->status
        );
        Preferences::where('id', $request->id)->update($data);
        echo json_encode(array('status' => 'true'));
    }

    function modify_rule($id)
    {
        $data['preferences'] = Preferences::find($id);
        $data['rules'] = Rules::where('preferences_id', $id)->get();

        echo json_encode($data);
    }

    function delete_rule($id)
    {
        Preferences::where('id', $id)->delete();
        echo json_encode(array('status' => 'true'));
    }

    function update_rule(Request $request)
    {
        $data = array(
            'seller_id' => Session()->get('MySeller')->id,
            'rule_name' => $request->name,
            'priority' => $request->priority,
            'match_type' => $request->filter_type,
            'priority1' => $request->courier_priority_1,
            'priority2' => $request->courier_priority_2,
            'priority3' => $request->courier_priority_3,
            'priority4' => $request->courier_priority_4,
        );

        Preferences::where('id', $request->pid)->update($data);

        Rules::where('preferences_id', $request->pid)->delete();

        // $n = count($request->product_name);
        foreach ($request->filter as $r) {
            $matchValue = $r['field'] == "weight" ? ($r['value'] * 1000) : $r['value'];
            $data_rules = array(
                'preferences_id' => $request->pid,
                'criteria' =>   $r['field'],
                'match_type' =>  $r['condition'],
                'match_value' => $matchValue
            );
            Rules::create($data_rules);
        }
        // generating notification
        $this->utilities->generate_notification('Success', 'Rules Updated successfully', 'success');
        return back();
    }


    function update_sku(Request $request)
    {
        $data = array(
            'sku' => $request->product_sku,
            'product_name' => $request->product_name,
            'product_price' => $request->product_price,
            'weight' => $request->weight,
            'length' => $request->length,
            'breadth' => $request->breadth,
            'height' => $request->height,
        );

        SKU::where('id', $request->sid)->update($data);

        // generating notification
        $this->utilities->generate_notification('Success', 'SKU Updated successfully', 'success');
        return back();
    }
}
