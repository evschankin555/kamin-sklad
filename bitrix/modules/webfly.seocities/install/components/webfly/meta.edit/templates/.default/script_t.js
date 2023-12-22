BX.ready(function(){
    var WFEditForm = new BX.PopupWindow("wfSeoEditPopup", null, {
        content: BX('wfSeoEditFormContainer').innerHTML,
        closeIcon: {right: "20px", top: "10px"},
        closeByEsc : true,
        autoHide : true,
        titleBar: {content: BX.create("span", {html: BX('wfSeoEditLink').innerHTML, 'props': {'className': 'access-title-bar'}})},
        zIndex: 0,
        offsetLeft: 0,
        offsetTop: 0,
        draggable: {restrict: false},
        overlay: {backgroundColor: 'black', opacity: '80' }
    });


    $(document).on('click','#wfSeoEditLink',function(){
        WFEditForm.show();
        return false;
    });

});