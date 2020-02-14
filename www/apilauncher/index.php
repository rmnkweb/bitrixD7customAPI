<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

use Bitrix\Main\Page\Asset;

Asset::getInstance()->addCss("/apilauncher/css/style.css");
Asset::getInstance()->addJs("/apilauncher/js/jquery-3.4.1.min.js");
Asset::getInstance()->addJs("/apilauncher/js/script.js");
$APPLICATION->SetTitle("Test API");

// Не самое оптимизированное решение, но для теста подойдёт :)
$allItems = [];
$res = CIBlockElement::GetList(["SORT" => "ASC"], ["IBLOCK_ID" => 22], false, false);
while($ob = $res->GetNextElement()) {
    $arFields = $ob->GetFields();
    $arProps = $ob->GetProperties();
    $allItems[] = [
        "ID" => $arFields["ID"],
        "NAME" => mb_convert_encoding($arFields["NAME"], "Windows-1251", "UTF-8"),
        "ADDRESS" => mb_convert_encoding($arProps["ADDRESS"]["VALUE"], "Windows-1251", "UTF-8"),
        "PHONE" => mb_convert_encoding($arProps["PHONE"]["VALUE"], "Windows-1251", "UTF-8"),
        "TIME" => mb_convert_encoding($arProps["TIME"]["VALUE"], "Windows-1251", "UTF-8"),
        "TYPE" => $arProps["TYPE"]["VALUE_ENUM_ID"],
    ];
}
?>
<script>
    let $allItemsObj = <?=json_encode($allItems)?>;
</script>
<form method="post" action="/api/" id="apiLauncherActualForm">
    <input type="hidden" name="json" id="apiLauncherActualFormJSON" />
</form>
<div class="apiLauncher">
    <div class="apiLauncherItem">
        <div class="apiLauncherItemTitle">
            Create
        </div>
        <form class="apiLauncherItemForm">
            <input type="hidden" name="action" value="create" />
            <div class="apiLauncherItemForm-row">
                <label>NAME:</label>
                <input type="text" name="NAME" />
            </div>
            <div class="apiLauncherItemForm-row">
                <label>ADDRESS:</label>
                <input type="text" name="ADDRESS" />
            </div>
            <div class="apiLauncherItemForm-row">
                <label>PHONE:</label>
                <input type="text" name="PHONE" />
            </div>
            <div class="apiLauncherItemForm-row">
                <label>TIME:</label>
                <input type="text" name="TIME" />
            </div>
            <div class="apiLauncherItemForm-row">
                <label>TYPE:</label>
                <select name="TYPE">
                    <option value="7">Обычный</option>
                    <option value="8">VIP</option>
                    <option value="9">Круглосуточный</option>
                </select>
            </div>
            <div class="apiLauncherItemForm-row">
                <button type="submit">Выполнить</button>
            </div>
        </form>
    </div>
    <div class="apiLauncherItem">
        <div class="apiLauncherItemTitle">
            Edit
        </div>
        <form class="apiLauncherItemForm">
            <input type="hidden" name="action" value="edit" />
            <div class="apiLauncherItemForm-row">
                <label>Элемент:</label>
                <select name="ELEM" id="editElementSelector">
                    <option value="0">Выберите элемент</option>
                    <?foreach ($allItems as $item) : ?>
                        <option value="<?=$item["ID"]?>"><?=$item["NAME"]?></option>
                    <?endforeach;?>
                </select>
            </div>
            <div class="apiLauncherItemForm-row">
                <label>NAME:</label>
                <input type="text" name="NAME" class="disabledUntilElementSelected" id="editElementField_NAME" disabled />
            </div>
            <div class="apiLauncherItemForm-row">
                <label>ADDRESS:</label>
                <input type="text" name="ADDRESS" class="disabledUntilElementSelected" id="editElementField_ADDRESS" disabled />
            </div>
            <div class="apiLauncherItemForm-row">
                <label>PHONE:</label>
                <input type="text" name="PHONE" class="disabledUntilElementSelected" id="editElementField_PHONE" disabled />
            </div>
            <div class="apiLauncherItemForm-row">
                <label>TIME:</label>
                <input type="text" name="TIME" class="disabledUntilElementSelected" id="editElementField_TIME" disabled />
            </div>
            <div class="apiLauncherItemForm-row">
                <label>TYPE:</label>
                <select name="TYPE" class="disabledUntilElementSelected" id="editElementField_TYPE" disabled>
                    <option value="0">Все</option>
                    <option value="7">Обычный</option>
                    <option value="8">VIP</option>
                    <option value="9">Круглосуточный</option>
                </select>
            </div>
            <div class="apiLauncherItemForm-row">
                <button type="submit">Выполнить</button>
            </div>
        </form>
    </div>
    <div class="apiLauncherItem">
        <div class="apiLauncherItemTitle">
            List / Filter
        </div>
        <form class="apiLauncherItemForm">
            <input type="hidden" name="action" value="list" />
            <div class="apiLauncherItemForm-row">
                <label>TYPE:</label>
                <select name="TYPE">
                    <option value="0">Все</option>
                    <option value="7">Обычный</option>
                    <option value="8">VIP</option>
                    <option value="9">Круглосуточный</option>
                </select>
            </div>
            <div class="apiLauncherItemForm-row">
                <button type="submit">Выполнить</button>
            </div>
        </form>
    </div>
    <div class="apiLauncherItem">
        <div class="apiLauncherItemTitle">
            Delete
        </div>
        <form class="apiLauncherItemForm">
            <input type="hidden" name="action" value="delete" />
            <div class="apiLauncherItemForm-row">
                <label>Элемент:</label>
                <select name="ELEM">
                    <?foreach ($allItems as $item) : ?>
                        <option value="<?=$item["ID"]?>"><?=$item["NAME"]?></option>
                    <?endforeach;?>
                </select>
            </div>
            <div class="apiLauncherItemForm-row">
                <button type="submit">Выполнить</button>
            </div>
        </form>
    </div>
</div>
<div class="apiLauncherResult hidden" id="apiLauncherResult">
    <div class="apiLauncherResultTitle">Результат</div>
    <div class="apiLauncherResultJSON">

    </div>
</div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>