/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Other/javascript.js to edit this template
 */

// Global variables
var cssList = {};
var objCss = {};
var cssListAll = [];

// Static Tag list to check and help map frame name to fields
var tagList = {'form': ['form', 'table'],
    'button': ['button', 'btn'],
    'textInput': ['input', 'inp', 'text field'],
    'row': ['row'],
    'text': ['text', 'txt']
};
var textInclude = ['font-family', 'font-style', 'font-weight', 'font-size', 'line-height', 'color'];
// Function to fetch CSS properties
function getFrameData() {
    // Make the AJAX request to fetch CSS properties

    // Clean the raw data by removing unnecessary tags
    var sampleCss = document.getElementById('cssCode').value.replaceAll('\n', '');
    sampleCss = sampleCss.replaceAll('/* Auto layout */', '');
    sampleCss = sampleCss.replaceAll('/* Inside auto layout */', '');
    sampleCss = sampleCss.replaceAll('/* identical to box height */', '');
    
    // Function to add !important and ';' to each css property extracted 
    function addSemicolon(item) {
        return item + ' !important;';
    }
    
    // Initialize frame name for previous iteration
    var lastClassNameTag = 'form_form_1';

    // Go through the string input until '*/' is not found
    while (sampleCss.indexOf('*/') !== -1) {
        
        var classNameTag = 'form' + '_';
        var j = sampleCss.slice(sampleCss.indexOf('/*') + 2, sampleCss.indexOf('*/')).trim(); // Save the data extracted from inside the comments
        
        // Find if the frame has tags from tagList and update the count of such tags in cssList
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
        classNameTag = classNameTag + j + '_' + cssList[j]; // 
        objCss[classNameTag] = '';
        
        // For assigning text values to button and input, and not to row
        if (!classNameTag.includes('row_') && !lastClassNameTag.includes('button_') && !lastClassNameTag.includes('Input_')) {
            cssListAll.push(classNameTag);
        }
        
        // Update the input string by slicing the parsed content
        sampleCss = sampleCss.slice(sampleCss.indexOf('*/') + 2).trim();
        
        if (sampleCss.indexOf('/*') !== -1) { // Split the property string and save as individual property: value string 
            objCss[classNameTag] = sampleCss.slice(0, sampleCss.indexOf('/*')).trim().split(';');
            objCss[classNameTag].pop();
            objCss[classNameTag].forEach((element, index, arr) => {
                arr[index] = addSemicolon(element);
            });
            sampleCss = sampleCss.slice(sampleCss.indexOf('/*'));
        } else { // For the last entry
            objCss[classNameTag] = sampleCss.trim().split(';');
            objCss[classNameTag].pop();
            objCss[classNameTag].forEach((element, index, arr) => {
                arr[index] = addSemicolon(element);
            });
            sampleCss = '';
        }

        // To assign text properties to input and buttons and not to row
        if (classNameTag.includes('text_') && !lastClassNameTag.includes('row_')) {
            for (var i = 0; i < objCss[classNameTag].length; i++){
                if (textInclude.some(substring => objCss[classNameTag][i].includes(substring))){
                    objCss[lastClassNameTag] = objCss[lastClassNameTag].concat(objCss[classNameTag][i]);
                }
            }
        }
        lastClassNameTag = classNameTag; // Update last class name
    }


    JSON.stringify(objCss);
//    JSON.stringify(cssListAll);

    // Update the UI for listing frame names
    var frameList = document.getElementById('frameName');
    while (frameList.options.length > 0) {                
        frameList.remove(0);
    } 
    for (var i = 0; i < cssListAll.length; i++) {
        frameList.options[frameList.options.length] = new Option(cssListAll[i], cssListAll[i]);
    }
}

// Function to pass objCss to controller 
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

// Function to disable the Get Frames button once clicked
function submitbtn(){
    document.getElementById("getFramesBtn").disabled=true;
    console.log('btn disabled');
}
