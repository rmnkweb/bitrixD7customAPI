<?php
use \Bitrix\Main\Loader;
use \Bitrix\Main\Application;
use \Bitrix\Iblock;
use Bitrix\Highloadblock\HighloadBlockTable;


if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

class OfficeCustomRestAPIComponent extends CBitrixComponent {
    private $iblock_id;
    private $hliblock_id;

    /**
     * @param CBitrixComponent | null $component
     */
    public function __construct($component = null) {
        $this->iblock_id = 22; // ID инфоблока
        $this->hliblock_id = 2; // ID highload-инфоблока

        parent::__construct($component);
    }

    /**
     * @return bool
     * @throws Exception
     */
    private function checkModules() {
        if ((!Loader::includeModule('iblock')) || (!Loader::IncludeModule('highloadblock'))) {
            return false;
        }

        return true;
    }

    /**
     * @return CAllMain|CMain
     */
    private function getApp() {
        global $APPLICATION;
        return $APPLICATION;
    }

    /**
     * @return CAllUser|CUser
     */
    private function getUser() {
        global $USER;
        return $USER;
    }

    /**
     * @param $arParams
     * @return mixed
     */
    public function onPrepareComponentParams($arParams) {
        return $arParams;
    }

    public function executeComponent() {
        if ($this->checkModules()) {

            $apiResult = $this->handleRequest($this->request);

        } else {
            $apiResult = [
                "status" => "2",
                "error" => "Bitrix module include error."
            ];
        }

        $apiResultJSON = json_encode($apiResult);
        echo $apiResultJSON;

        // $this->includeComponentTemplate();
    }

    /**
     * @param $request
     * @return array
     */
    private function handleRequest($request) {

        if ($request->isPost()) {

            if ($this->bearerTokenVerify()) {
                $input = json_decode($request["json"], true);
                $apiResult = $this->apiActions($input);
            } else {
                $apiResult = [
                    "status" => "2",
                    "error" => "API token mismatch."
                ];
            }
        } else {
            $apiResult = [
                "status" => "2",
                "error" => "Only POST request supported."
            ];
        }

        return $apiResult;
    }

    /**
     * @param $input
     * @return array
     */
    private function apiActions($input) {
        if ((isset($input["action"])) && (!empty($input["action"]))) {

            switch ($input["action"]) {
                case "create":
                    $newElementVars = [
                        "ACTIVE" => "Y",
                        "IBLOCK_ID" => $this->iblock_id,
                        "IBLOCK_SECTION_ID" => false,
                    ];
                    $hlibElementVars = [];
                    $arProps = [];
                    if ((isset($input["NAME"])) && (!empty($input["NAME"]))) {
                        $newElementVars["NAME"] = htmlspecialchars($input["NAME"]);
                    } else {
                        $newElementVars["NAME"] = "Untitled";
                    }
                    $hlibElementVars["UF_OCRAH_NAME"] = $newElementVars["NAME"];

                    if ((isset($input["ADDRESS"])) && (!empty($input["ADDRESS"]))) {
                        $arProps["ADDRESS"] = htmlspecialchars($input["ADDRESS"]);
                        $hlibElementVars["UF_OCRAH_ADDRESS"] = $arProps["ADDRESS"];
                    }
                    if ((isset($input["PHONE"])) && (!empty($input["PHONE"]))) {
                        $arProps["PHONE"] = htmlspecialchars($input["PHONE"]);
                        $hlibElementVars["UF_OCRAH_PHONE"] = $arProps["PHONE"];
                    }
                    if ((isset($input["TIME"])) && (!empty($input["TIME"]))) {
                        $arProps["TIME"] = htmlspecialchars($input["TIME"]);
                        $hlibElementVars["UF_OCRAH_TIME"] = $arProps["TIME"];
                    }
                    if ((isset($input["TYPE"])) && (!empty($input["TYPE"]))) {
                        $typeValue = (int) htmlspecialchars($input["TYPE"]);
                        $arProps["TYPE"] = ["VALUE" => $typeValue];
                        $hlibElementVars["UF_OCRAH_TYPE"] = $this->convertHLIBTypeValue($typeValue);
                    }
                    $newElementVars["PROPERTY_VALUES"] = $arProps;

                    $iblockElement = new CIBlockElement;
                    if ($newElementID = $iblockElement->Add($newElementVars)) {
                        $hlibElementVars["UF_OCRAH_IBELEM"] = $newElementID;

                        $entity_data_class = $this->getHLIBEntityDataClass($this->hliblock_id);

                        if ($entity_data_class::add($hlibElementVars)) {
                            $apiResult = [
                                "status" => "1",
                                "error" => ""
                            ];
                        } else {
                            $apiResult = [
                                "status" => "3",
                                "error" => "Highload element create error, but iBlock element created."
                            ];
                        }
                    } else {
                        $apiResult = [
                            "status" => "2",
                            "error" => "iBlock element create error."
                        ];
                    }
                    break;
                case "edit":
                    if ((isset($input["ELEM"])) && (!empty($input["ELEM"]))) {
                        $updateElementVars = [];
                        $arProps = [];
                        $hlibElementVars = [];
                        if ((isset($input["NAME"])) && (!empty($input["NAME"]))) {
                            $updateElementVars["NAME"] = htmlspecialchars($input["NAME"]);
                        } else {
                            $updateElementVars["NAME"] = "Untitled";
                        }
                        $hlibElementVars["UF_OCRAH_NAME"] = $updateElementVars["NAME"];

                        if ((isset($input["ADDRESS"])) && (!empty($input["ADDRESS"]))) {
                            $arProps["ADDRESS"] = htmlspecialchars($input["ADDRESS"]);
                            $hlibElementVars["UF_OCRAH_ADDRESS"] = $arProps["ADDRESS"];
                        }
                        if ((isset($input["PHONE"])) && (!empty($input["PHONE"]))) {
                            $arProps["PHONE"] = htmlspecialchars($input["PHONE"]);
                            $hlibElementVars["UF_OCRAH_PHONE"] = $arProps["PHONE"];
                        }
                        if ((isset($input["TIME"])) && (!empty($input["TIME"]))) {
                            $arProps["TIME"] = htmlspecialchars($input["TIME"]);
                            $hlibElementVars["UF_OCRAH_TIME"] = $arProps["TIME"];
                        }
                        if ((isset($input["TYPE"])) && (!empty($input["TYPE"]))) {
                            $typeValue = (int) htmlspecialchars($input["TYPE"]);
                            $arProps["TYPE"] = ["VALUE" => $typeValue];
                            $hlibElementVars["UF_OCRAH_TYPE"] = $this->convertHLIBTypeValue($typeValue);
                        }
                        $updateElementVars["PROPERTY_VALUES"] = $arProps;

                        $iblockElement = new CIBlockElement;
                        if ($iblockElement->Update($input["ELEM"], $updateElementVars)) {

                            // Highload edit
                            $entity_data_class = $this->getHLIBEntityDataClass($this->hliblock_id);
                            $rsData = $entity_data_class::getList(array(
                                'select' => array("ID"),
                                'limit' => '1',
                                'filter' => array('UF_OCRAH_IBELEM' => $input["ELEM"])
                            ));
                            $hlibElementUpdated = false;
                            while($hlibElement = $rsData->fetch()){
                                if ($entity_data_class::update($hlibElement["ID"], $hlibElementVars)) {
                                    $hlibElementUpdated = true;
                                }
                            }

                            if ($hlibElementUpdated) {
                                $apiResult = [
                                    "status" => "1",
                                    "error" => ""
                                ];
                            } else {
                                $apiResult = [
                                    "status" => "3",
                                    "error" => "Highload element update error, but iBlock element updated."
                                ];
                            }
                        } else {
                            $apiResult = [
                                "status" => "2",
                                "error" => "iBlock element create error."
                            ];
                        }
                    } else {
                        $apiResult = [
                            "status" => "2",
                            "error" => "ID is not set."
                        ];
                    }
                    break;
                case "list":
                    $arFilter = [
                        "IBLOCK_ID" => $this->iblock_id
                    ];
                    if ((isset($input["TYPE"])) && (!empty($input["TYPE"]))) {
                        $arFilter["PROPERTY_TYPE"] = $input["TYPE"];
                    }
                    $iblockElement = new CIBlockElement;
                    if ($res = $iblockElement->GetList(["SORT" => "ASC"], $arFilter, false, false)) {
                        $items = [];
                        while($ob = $res->GetNextElement()) {
                            $arFields = $ob->GetFields();
                            $arProps = $ob->GetProperties();
                            $items[] = [
                                "ID" => $arFields["ID"],
                                "NAME" => mb_convert_encoding($arFields["NAME"], "Windows-1251", "UTF-8"),
                                "ADDRESS" => mb_convert_encoding($arProps["ADDRESS"]["VALUE"], "Windows-1251", "UTF-8"),
                                "PHONE" => mb_convert_encoding($arProps["PHONE"]["VALUE"], "Windows-1251", "UTF-8"),
                                "TIME" => mb_convert_encoding($arProps["TIME"]["VALUE"], "Windows-1251", "UTF-8"),
                                "TYPE" => mb_convert_encoding($arProps["TYPE"]["VALUE"], "Windows-1251", "UTF-8"),
                            ];
                        }
                        $apiResult = [
                            "items" => $items,
                            "status" => "1",
                            "error" => ""
                        ];
                    } else {
                        $apiResult = [
                            "status" => "2",
                            "error" => "iBlock list error."
                        ];
                    }
                    break;
                case "delete":
                    $iblockElement = new CIBlockElement;
                    $iblockID = $iblockElement->GetIBlockByID($input["ELEM"]);
                    if ($iblockID === $this->iblock_id) {
                        if (CIBlockElement::Delete($input["ELEM"])) {

                            // Highload delete
                            $entity_data_class = $this->getHLIBEntityDataClass($this->hliblock_id);
                            $rsData = $entity_data_class::getList(array(
                                'select' => array("ID"),
                                'limit' => '1',
                                'filter' => array('UF_OCRAH_IBELEM' => $input["ELEM"])
                            ));
                            $hlibElementDeleted = false;
                            while($hlibElement = $rsData->fetch()){
                                if ($entity_data_class::delete($hlibElement["ID"])) {
                                    $hlibElementDeleted = true;
                                }
                            }

                            if ($hlibElementDeleted) {
                                $apiResult = [
                                    "status" => "1",
                                    "error" => ""
                                ];
                            } else {
                                $apiResult = [
                                    "status" => "3",
                                    "error" => "Highload element delete error, but iBlock element deleted."
                                ];
                            }
                        } else {
                            $apiResult = [
                                "status" => "2",
                                "error" => "iBlock element delete error."
                            ];
                        }
                    } else {
                        $apiResult = [
                            "status" => "2",
                            "error" => "Wrong element ID provided."
                        ];
                    }
                    break;
                default:
                    $apiResult = [
                        "status" => "2",
                        "error" => "Unknown action."
                    ];
            }
        } else {
            $apiResult = [
                "status" => "2",
                "error" => "No action been set."
            ];
        }

        return $apiResult;
    }

    private function getHLIBEntityDataClass($id) {
        if (empty($id) || $id < 1)
        {
            return false;
        }
        $hlblock = Bitrix\Highloadblock\HighloadBlockTable::getById($id)->fetch();
        $entity = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
        $entityDataClass = $entity->getDataClass();
        return $entityDataClass;
    }

    private function convertHLIBTypeValue($typeID) {
        switch($typeID) {
            case 7:
                return 4;
                break;
            case 8:
                return 5;
                break;
            case 9:
                return 6;
                break;
        }
        return false;
    }

    private function bearerTokenVerify() {
        $headers = $this->getAuthorizationHeader();
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                if ($matches[1] === "080042cad6356ad5dc0a720c18b53b8e53d4c274") {
                    return true;
                } else {
                    return false;
                }
            }
        }
        return null;
    }

    private function getAuthorizationHeader() {
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }
}