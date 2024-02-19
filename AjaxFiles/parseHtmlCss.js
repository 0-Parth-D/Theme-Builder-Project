/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Other/javascript.js to edit this template
 */

$(document).ready(function () {
    // Event handler for page load
    $(window).on('load', function () {
        // Get the controller and action names

    });
});
var cssList = {};
var objCss = {};
var cssListAll = [];
var tagList = {'form': ['form', 'table'],
    'button': ['button', 'btn'],
    'textInput': ['input', 'inp', 'text field'],
    'row': ['row'],
    'text': ['text', 'txt']
};
// Function to fetch CSS properties
function getFrameData() {
    // Make the AJAX request to fetch CSS properties

    // Create a style element and append it to the head
    var sampleCss = document.getElementById('cssCode').value.replaceAll('\n', '');
    sampleCss = sampleCss.replaceAll('/* Auto layout */', '');
    sampleCss = sampleCss.replaceAll('/* Inside auto layout */', '');
    function addSemicolon(item) {
        return item + ';';
    }
    while (sampleCss.indexOf('*/') !== -1) {
        var classNameTag = 'form' + '_';
        var j = sampleCss.slice(sampleCss.indexOf('/*') + 2, sampleCss.indexOf('*/')).trim();
        for (const property in tagList) {
            if (tagList[property].some(substring => j.toLowerCase().includes(substring))) {
                j = property;
                if (cssList.hasOwnProperty(j)) {
                    cssList[j]++;
                } else {
                    cssList[j] = 1;
                }
                break;
            }
        }
//        console.log(cssList);
        classNameTag = classNameTag + j + '_' + cssList[j];
//        cssList.push(classNameTag);
        objCss[classNameTag] = '';
        if (!classNameTag.includes('text_') && !classNameTag.includes('row_')) {
            cssListAll.push(classNameTag);
        }
        sampleCss = sampleCss.slice(sampleCss.indexOf('*/') + 2).trim();
        if (sampleCss.indexOf('/*') !== -1) {
            objCss[classNameTag] = sampleCss.slice(0, sampleCss.indexOf('/*')).trim().split(';');
            objCss[classNameTag].pop();
            objCss[classNameTag].forEach((element, index, arr) => {
                arr[index] = addSemicolon(element);
            });
            sampleCss = sampleCss.slice(sampleCss.indexOf('/*'));
        } else {
            objCss[classNameTag] = sampleCss.trim().split(';');
            objCss[classNameTag].pop();
            objCss[classNameTag].forEach((element, index, arr) => {
                arr[index] = addSemicolon(element);
            });
            sampleCss = '';
        }
    }

    JSON.stringify(objCss);
//    JSON.stringify(cssListAll);
    var frameList = document.getElementById('frameName');
    while (frameList.options.length > 0) {                
        frameList.remove(0);
    } 
    for (var i = 0; i < cssListAll.length; i++) {
        frameList.options[frameList.options.length] = new Option(cssListAll[i], cssListAll[i]);
    }
    
//    $("input[name='FormFieldFigmaMapping[frame_name]']").val(cssListAll);

}

function saveCssProperties() {
        $.ajax({
            url: 'index.php?r=formFieldCsspropertyValueMapping/passingPostToParser',
            type: 'POST',
            data: {objCss: JSON.stringify(objCss)},
            success: function (objCss) {
                console.log(objCss);
            },

            error: function (jqXHR, textStatus, errorThrown) {
                // Handle the error case here
                console.error('AJAX request failed:', textStatus, errorThrown);
            }
        });
}

function submitbtn(){
    document.getElementById("getFramesBtn").disabled=true;
    console.log('btn disabled');
}
