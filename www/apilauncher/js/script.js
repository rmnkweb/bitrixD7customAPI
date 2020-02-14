$(document).ready(function () {
    let $allItems = [];

    $.each($allItemsObj, function (key, item) {
        $allItems[item.ID] = item;
    });

    $("#editElementSelector").on("change", function () {
        let $id = $(this).val();

        if (($id) && (typeof $allItems[$id] !== "undefined")) {
            if (typeof $allItems[$id].NAME !== "undefined") {
                $("#editElementField_NAME").val($allItems[$id].NAME);
            }
            if (typeof $allItems[$id].ADDRESS !== "undefined") {
                $("#editElementField_ADDRESS").val($allItems[$id].ADDRESS);
            }
            if (typeof $allItems[$id].PHONE !== "undefined") {
                $("#editElementField_PHONE").val($allItems[$id].PHONE);
            }
            if (typeof $allItems[$id].TIME !== "undefined") {
                $("#editElementField_TIME").val($allItems[$id].TIME);
            }
            $("#editElementField_TYPE option").prop("selected", false);
            if (typeof $allItems[$id].TYPE !== "undefined") {
                $("#editElementField_TYPE option[value="+ $allItems[$id].TYPE +"]").prop("selected", true);
            }

            $(".disabledUntilElementSelected").attr("disabled", false);
        } else {
            $("#editElementField_NAME").val("");
            $("#editElementField_ADDRESS").val("");
            $("#editElementField_PHONE").val("");
            $("#editElementField_TIME").val("");
            $(".disabledUntilElementSelected").attr("disabled", false);
        }
    });

    $(".apiLauncherItemForm").on("submit", function (e) {
        e.preventDefault();
        let formData = new FormData(this),
            object = {},
            preparedData;

        formData.forEach(function(value, key){
            object[key] = value;
        });
        preparedData = JSON.stringify(object);

        $("#apiLauncherActualFormJSON").val(preparedData);

        console.log(preparedData);

        // $.ajax({
        //     type: $("#apiLauncherActualForm").attr("method"),
        //     url: $("#apiLauncherActualForm").attr("action"),
        //     headers: {
        //         "Authorization": "Bearer 080042cad6356ad5dc0a720c18b53b8e53d4c274"
        //     },
        //     data: {json: preparedData},
        //     dataType: "json",
        //     success: function (result) {
        //         $("#apiLauncherResult").removeClass("hidden");
        //         $("#apiLauncherResult").text(JSON.stringify(result));
        //     }
        // });
    });



});