<?php

/**
 * @author owliber
 * @date Oct 2, 2012
 * @filename CodeGenerator.php
 * 
 */
class CodeGenerator extends Controller
{

    /**
     * 
     * @param int $length
     * @param int $num
     * @return type
     */        
    public function generateCode($length, $num)
    {
        $numeric_set = '0123456789';
        $character_set = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $code_sets = $numeric_set . $character_set;
        
        $codes = array();
        $num_length = strlen($code_sets);
        $num_created = 0;
        
        while ($num_created < $num)
        {
            $code = 'P' . CodeGenerator::generate_codes($length, $code_sets, $num_length);
            if (isset($codes[$code]))
                continue;
            $codes[$code] = true;
            $num_created++;
        }
        return array_keys($codes);
    }
    
    public function generate_codes($length, $code_sets, $num_length)
    {
        $code = '';
        while (strlen($code) < $length)
            $code .= CodeGenerator::get_random_code($code_sets, $num_length);
        return $code;
    }
    
    public function get_random_code($code_sets, $num_length)
    {
        return substr($code_sets, mt_rand(0, $num_length - 1), 1);
    }
    
    public function generate_str_codes($id, $qty)
    {
        $codes = array();
        
        // generate the codes
        $activationCodes = CodeGenerator::generateCode(19, $qty);
        for($i = 0; $i < $qty; $i++)
        {
            $codes[] = "('".$activationCodes[$i]."'," . $id . ")";
        }
        $final = implode(",", $codes); 
        
        return $final;
    }
}
?>
