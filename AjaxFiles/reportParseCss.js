// Global variables
var objCss = {};
var cssListAll = [];

// Static Tag list to check and help map frame name to fields
var tagList = {'form': ['form', 'table'],
    'button': ['button', 'btn'],
    'textInput': ['input', 'inp', 'text field'],
    'cellText': ['cell text', 'data'],
    'headerText': ['column', 'header', 'header text']
};

// Function to fetch CSS properties
function getFrameDataReport() {
    // Make the AJAX request to fetch CSS properties

    // Clean the raw data by removing unnecessary tags
    var sampleCss = document.getElementById('cssCode').value.replaceAll('\n', '');
    sampleCss = sampleCss.replaceAll('/* Auto layout */', '');
    sampleCss = sampleCss.replaceAll('/* Inside auto layout */', '');
    sampleCss = sampleCss.replaceAll('/* Cell Text */', '');
    sampleCss = sampleCss.replaceAll('/* Header Text */', '');
    sampleCss = sampleCss.replaceAll('/* identical to box height */', '');

    // Function to add !important and ';' to each css property extracted 
    function addSemicolon(item) {
        return item + ' !important;';
    }

    // Go through the string input until '*/' is not found
    while (sampleCss.indexOf('*/') !== -1) {

        var classNameTag = 'report' + '_';
        var j = sampleCss.slice(sampleCss.indexOf('/*') + 2, sampleCss.indexOf('*/')).trim(); // Save the data extracted from inside the comments

        // Find if the frame has tags from tagList and update the count of such tags in cssList
        for (const property in tagList) {
            if (tagList[property].some(substring => j.toLowerCase().includes(substring))) {
                j = property;
                break;
            }
        }
        classNameTag = classNameTag + j ; // 
        objCss[classNameTag] = '';

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
    }
    
    // Update the UI for listing frame names
    var frameList = document.getElementById('frameNameReport');
    console.log(frameList);
    while (frameList.options.length > 0) {                
        frameList.remove(0);
    } 
    for (var i = 0; i < Object.keys(objCss).length; i++) {
        frameList.options[frameList.options.length] = new Option(Object.keys(objCss)[i], Object.keys(objCss)[i]);
    }
    console.log(Object.keys(objCss)[0]);
    
//    console.log(objCss);
//    saveCssProperties();
}

// Function to pass objCss to controller 
function saveCssProperties() {
    $.ajax({
        url: 'index.php?r=themeForReport/reportFrameMap',
        type: 'POST',
        data: {objCss: JSON.stringify(objCss), mappingList: JSON.stringify(mappingList), themeName: document.getElementById('themeName').value},
        success: function (objCss, mappingList, themeName) {
            console.log(mappingList);
            console.log(objCss);
            console.log(themeName);
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