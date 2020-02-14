<?php
class curlTest {
    private $apiURL;

    public function __construct() {
        $this->apiURL = 'http://bitrixdev.local/api/';

        // $this->createAction();
        // $this->editAction();
        // $this->listAction();
        $this->deleteAction();
    }

    private function createAction() {
        if( $curl = curl_init() ) {
            curl_setopt($curl, CURLOPT_URL, $this->apiURL);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, ["json" => "{\"action\":\"create\",\"NAME\":\"testcurl\",\"ADDRESS\":\"testcurl1\",\"PHONE\":\"testcurl2\",\"TIME\":\"testcurl3\",\"TYPE\":\"8\"}"]);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                "Authorization: Bearer 080042cad6356ad5dc0a720c18b53b8e53d4c274",
            ));
            $out = curl_exec($curl);
            echo "Response: "  . $out;
            curl_close($curl);
        } else {
            echo "curl init error!";
        }
    }

    private function editAction() {
        if( $curl = curl_init() ) {
            curl_setopt($curl, CURLOPT_URL, $this->apiURL);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, ["json" => "{\"action\":\"edit\",\"ELEM\":\"371\",\"NAME\":\"testcurlEdited\",\"ADDRESS\":\"testcurl1Edited\",\"PHONE\":\"testcurl2Edited\",\"TIME\":\"testcurl3Edited\",\"TYPE\":\"9\"}"]);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                "Authorization: Bearer 080042cad6356ad5dc0a720c18b53b8e53d4c274",
            ));
            $out = curl_exec($curl);
            echo "Response: "  . $out;
            curl_close($curl);
        } else {
            echo "curl init error!";
        }
    }

    private function listAction() {
        if( $curl = curl_init() ) {
            curl_setopt($curl, CURLOPT_URL, $this->apiURL);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, ["json" => "{\"action\":\"list\",\"TYPE\":\"0\"}"]);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                "Authorization: Bearer 080042cad6356ad5dc0a720c18b53b8e53d4c274",
            ));
            $out = curl_exec($curl);
            echo "Response: "  . $out;
            curl_close($curl);
        } else {
            echo "curl init error!";
        }
    }

    private function deleteAction() {
        if( $curl = curl_init() ) {
            curl_setopt($curl, CURLOPT_URL, $this->apiURL);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, ["json" => "{\"action\":\"delete\",\"ELEM\":\"371\"}"]);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                "Authorization: Bearer 080042cad6356ad5dc0a720c18b53b8e53d4c274",
            ));
            $out = curl_exec($curl);
            echo "Response: "  . $out;
            curl_close($curl);
        } else {
            echo "curl init error!";
        }
    }
}

new curlTest();