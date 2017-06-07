<?php

namespace fxplusCustom\RecordDriver;

class SolrMarc extends \VuFind\RecordDriver\SolrMarc
 {
  /**
     * Get the indexed in of the current record.
     *
     * @return array
     */
    public function getIndexedIn()
    {
        return $this->getFieldArray('510');
    }
    /**
     * Get the older issues of the current record.
     *
     * @return array
     */
    public function getOlderIssues($location)
    {
        $issueStrings = [];
        $xsubfield = [];
        if ($fields = $this->getMarcRecord()->getFields('866')) {
            $i=0;
            foreach ($fields as $field) {
                //get all 866 subfields
                $subfields = $field->getSubfields();
                $subdata='';
                $subkey='';
                $xsubfield = $this->getSubfieldArray($field, ['x']);
                if($xsubfield[0] == $location) {
                  foreach ($subfields as $subfield) {
                    if ($subfield->getCode()== 'a') {
                        $subdata .= $subfield->getData();
                    }
                    //$issueStrings[] = $this->getSubfieldArray($field, ['a']);
                  }
                  $issueStrings[$i] = $subdata;
                }
                $i++;
            }
        }
        return $issueStrings;
        //return $this->getFieldArray('866');
    }
    /**
     * Get the other older issues of the current record from 863 fields
     *
     * @return array
     */
    public function getOtherOlderIssues($location)
    {
        $sub_format=[];
        //$sub_b_format=[];

        if ($fields = $this->getMarcRecord()->getFields('853')) {
            foreach ($fields as $field) {
                //get all 863 subfields
                $subfields = $field->getSubfields();
                $subfdata='';
                $subfrun='';
                $subffield='';
                foreach ($subfields as $subfield) {
                   if (is_numeric($subfield->getCode())) {
                        //this is the run
                        $subfrun = $subfield->getData();
                        $subfrun = intval($subfrun);
                    }
                   if ($subfield->getCode()== 'a') {
                        $subffield = 'a';
                        $subfdata = $subfield->getData();
                        $sub_format[$subfrun][$subffield] = $subfdata;
                   }
                   if ($subfield->getCode()== 'b') {
                        $subffield = 'b';
                        $subfdata = $subfield->getData();
                        $sub_format[$subfrun][$subffield] = $subfdata;
                   }
                }

            }
        }

        $issueStrings = [];
        if ($fields = $this->getMarcRecord()->getFields('863')) {
            foreach ($fields as $field) {
                //get all 863 subfields
                $subfields = $field->getSubfields();
                $subdata='';
                $subkey='';
                $xsubfield = $this->getSubfieldArray($field, ['x']);
                if($xsubfield[0] == $location) {
                  foreach ($subfields as $subfield) {
                    if (is_numeric($subfield->getCode())) {
                        //this is the run.issue order
                        $subkey = $subfield->getData();
                        $chars = preg_split('/\./', $subkey, -1, PREG_SPLIT_NO_EMPTY);
                        $subrun = intval($chars[0]);
                        $subkey = intval($chars[1]);
                    }
                    if ($subfield->getCode()== 'a') {
                        $subdata .= $sub_format[$subrun][$subfield->getCode()] . ' ' . $subfield->getData() . ' ';
                    }
                    if ($subfield->getCode()== 'b') {
                        $subdata .= $sub_format[$subrun][$subfield->getCode()] . ' ' . $subfield->getData();
                    }
                    if ($subfield->getCode()== 'i') {
                        $subdata .= ' (' . $subfield->getData() . ')';
                    }
                    if ($subfield->getCode()== 'j') {
                        $subdata .= ' (' . $subfield->getData() . ')';
                    }
                  }
                  $issueStrings[$subkey] = $subdata;
                }
            }
        }
        return $issueStrings;
    }

    public function traverseArray($array)
    {
     // Loops through each element. If element again is array, function is recalled. If not, result is echoed.
     $arraystr = "";
     foreach ($array as $key => $value)
     {
        if (is_array($value))
        {
            $this->traverseArray($value); // Or
            // traverseArray($value);
        } elseif(is_object($value))
        {
            $this->traverseArray($value);
        }
        else
        {
            $arraystr .= $key . " = " . $value . " ";
            //echo $key . " = " . $value . "<br />\n";
        }
     }
     return $arraystr;
    }
    /**
     * Are any holdings location holdable.
     *
     * @return bool
     */
    public function isTitleHoldable($holdings)
    {
      $jcanwehold = "false";
      $invalid_refhold_locations = "W-ADAREF:W-DIS:T-FICREF:W-GCR1:W-IBREF:W-MREF:T-OFF:W-OREF:T-SSREF:T-MREF:T-OREF:T-VIDTMPRF:T-VIDREF:T-DVDREF:T-RAD:T-DIS:T-DLL:T-STOR-REF:T-STOR-PHD:W-OSZREF:T-STCK-ELM:T-JNREF:T-CDREF:CPRV:CPRJ:T-VINYLREF:E-JOURN:E-RES:E-BKX:E-DIS:E-BK:T-VST";
      $invalid_olhold_locations = "E-JOURN:E-RES:E-BKX:E-DIS:E-BK:T-VST";
      $invalid_refhold_locations_array = explode(":", $invalid_refhold_locations);
      $invalid_olhold_locations_array = explode(":", $invalid_olhold_locations);
      foreach ($holdings as $holding) {
          if ($fields = $this->getMarcRecord()->getFields('852')) {
            foreach ($fields as $field) {
                $bsubfield = $this->getSubfieldArray($field, ['b']);
                if (in_array($bsubfield[0], $invalid_olhold_locations_array)) {
                        $jcanwehold = "online";
                }
                if (!in_array($bsubfield[0], $invalid_refhold_locations_array)) {
                        $jcanwehold = "true";
                }
            }
          }
      }

      return $jcanwehold;
    }
 }