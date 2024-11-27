function CallCapture() {
    $(".large-loader").css("display", "block");

    var Device = GetDevice();
    var Service = GetServiceType();
    var AAdharNumber = $("#aadhar_no").val();
    var Amount = $("#amount").val();
    var MobileNumber = $("#mobile").val();
    var IIN = $("#bankID").val();
    if (AAdharNumber != "" && Amount != "" && MobileNumber != "" && IIN != "") {
        if (Service == "balinfo" || Service == "ministatement" || Service == "balwithdraw" || Service == "aadharpay") {
            if (Device == "MANTRA_PROTOBUF") {
                CaptureAvdm();
            } else if (Device == "MORPHO_PROTOBUF") {
                CaptureMorpho();
            } else {
                $(".large-loader").css("display", "none");
                alert("Please Select Device Type");
                ClearCtrl();
            }
        } else {
            $(".large-loader").css("display", "none");
            alert("Please Select Service");
            ClearCtrl();
        }
    } else {
        $(".large-loader").css("display", "none");
        alert("Please Enter Value in mandatory fileds");
        ClearCtrl();
    }
}

function CallMemberCapture() {
    $(".large-loader").css("display", "block");

    var Device = GetDevice();

    var AAdharNumber = $("#aadhar_no").val();
    var MobileNumber = $("#mobile").val();

    if (Device == "MANTRA_PROTOBUF") {
        Kyc2FaCapture();
    } else if (Device == "MORPHO_PROTOBUF") {
        Kyc2FaCapture();
    } else if (Device == "STARTEK.ACPL") {
        Capture2FaStartekScan();
    } else {
        $(".large-loader").css("display", "none");
        alert("Please Select Device Type");
        ClearCtrl();
    }
}

function CallMemberCaptureAp() {
    $(".large-loader").css("display", "block");

    var Device = GetDevice();
    var AAdharNumber = $("#aadhar_no").val();
    var MobileNumber = $("#mobile").val();

    if (Device == "MANTRA_PROTOBUF") {
        Kyc2FaApCapture();
    } else if (Device == "MORPHO_PROTOBUF") {
        Kyc2FaApCapture();
    } else {
        $(".large-loader").css("display", "none");
        alert("Please Select Device Type");
        ClearCtrl();
    }
}

function KycCapture() {
    var Device = GetDevice();

    if (Device == "MANTRA_PROTOBUF") {
        CaptureKycAvdm();
    } else if (Device == "MORPHO_PROTOBUF") {
        CaptureMorphoScan();
    } else {
        alert("Please Select Device Type");
    }
}

function Kyc2FaCapture() {
    var Device = GetDevice();

    if (Device == "MANTRA_PROTOBUF") {
        Capture2FaKycAvdm();
    } else if (Device == "MORPHO_PROTOBUF") {
        Capture2FaMorphoScan();
    } else if (Device == "STARTEK.ACPL") {
        Capture2FaStartekScan();
    } else {
        alert("Please Select Device Type");
    }
}

function Kyc2FaApCapture() {
    var Device = GetDevice();

    if (Device == "MANTRA_PROTOBUF") {
        Capture2FaKycApAvdm();
    } else if (Device == "MORPHO_PROTOBUF") {
        Capture2FaMorphoApScan();
    } else {
        alert("Please Select Device Type");
    }
}

//captcture mantra
function CaptureMorpho() {
    //debugger;
    var url = "http://127.0.0.1:11100/capture";
    if ($("#serviceType4").is(":checked")) {
        var PIDOPTS = '<PidOptions ver="1.0">' + '<Opts fCount="1" fType="2" iCount="" iType="" pCount="" pType="" format="0" pidVer="2.0" timeout="10000"  env="P" otp="" wadh="" posh=""/>' + "</PidOptions>";
    } else {
        var PIDOPTS = '<PidOptions ver="1.0">' + '<Opts fCount="1" fType="2" iCount="" iType="" pCount="" pType="" format="0" pidVer="2.0" timeout="10000"  env="P" otp="" wadh="" posh=""/>' + "</PidOptions>";
    }
    /*
                format=\"0\"     --> XML
                format=\"1\"     --> Protobuf   env=\"P\"
                */
    var xhr;
    var ua = window.navigator.userAgent;
    var msie = ua.indexOf("MSIE ");

    if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./)) {
        // If Internet Explorer, return version number
        //IE browser
        xhr = new ActiveXObject("Microsoft.XMLHTTP");
    } else {
        //other browser
        xhr = new XMLHttpRequest();
    }

    xhr.open("CAPTURE", url, true);
    xhr.setRequestHeader("Content-Type", "text/xml");
    xhr.setRequestHeader("Accept", "text/xml");

    xhr.onload = function () {
        //if(xhr.readyState == 1 && count == 0){
        //  fakeCall();
        //}
        if (xhr.readyState == 4) {
            var status = xhr.status;

            if (status == 200) {
                //console.log(xhr.responseText);
                //alert(xhr.responseText);
                //return false;
                var deviceIMEI = "";
                var IIN = $("#bankID").val();
                var Service = GetServiceType();
                if (Service == "balinfo" || Service == "ministatement") {
                    var Amount = $("#amount").val();
                    if (Amount == "0") {
                        CallApi(Service, xhr.responseText);
                    } else {
                        alert("Wrong Input Amount");
                        ClearCtrl();
                    }
                } else if (Service == "balwithdraw" || Service == "aadharpay") {
                    var Amount = $("#amount").val();
                    if (Amount <= 10000 && Amount >= 50) {
                        CallApi(Service, xhr.responseText);
                    } else {
                        alert("Amount should be less than 10000 and grater than or equal 50.");
                        ClearCtrl();
                    }
                }
            } else {
                alert("Something went wrong ! Please try again later.");
                ClearCtrl();
            }
        }
    };

    xhr.send(PIDOPTS);
}

//captcture mantra
function CaptureMorphoScan() {
    $(".large-loader").css("display", "block");
    //debugger;
    var url = "http://127.0.0.1:11100/capture";
    var PIDOPTS =
        '<PidOptions ver="1.0">' + '<Opts fCount="1" fType="2" iCount="" iType="" pCount="" pType="" format="0" pidVer="2.0" timeout="10000" wadh="E0jzJ/P8UopUHAieZn8CKqS4WPMi5ZSYXgfnlfkWjrc="  env="P" otp="" posh=""/>' + "</PidOptions>";
    /*
                format=\"0\"     --> XML
                format=\"1\"     --> Protobuf   env=\"P\"
                */
    var xhr;
    var ua = window.navigator.userAgent;
    var msie = ua.indexOf("MSIE ");

    if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./)) {
        // If Internet Explorer, return version number
        //IE browser
        xhr = new ActiveXObject("Microsoft.XMLHTTP");
    } else {
        //other browser
        xhr = new XMLHttpRequest();
    }

    xhr.open("CAPTURE", url, true);
    xhr.setRequestHeader("Content-Type", "text/xml");
    xhr.setRequestHeader("Accept", "text/xml");

    xhr.onload = function () {
        //if(xhr.readyState == 1 && count == 0){
        //  fakeCall();
        //}
        if (xhr.readyState == 4) {
            var status = xhr.status;
            $(".large-loader").css("display", "block");
            if (status == 200) {
                //console.log(xhr.responseText);
                //alert(xhr.responseText);
                //return false;
                CallKYCVerifyApi(xhr.responseText);
            } else {
                alert("Something went wrong ! Please try again later.");
                ClearCtrl();
            }
        }
    };

    xhr.send(PIDOPTS);
}

function Capture2FaMorphoScan() {
    $(".large-loader").css("display", "block");
    //debugger;
    var url = "http://127.0.0.1:11100/capture";
    var PIDOPTS = '<PidOptions ver="1.0">' + '<Opts fCount="1" fType="2" iCount="" iType="" pCount="" pType="" format="0" pidVer="2.0" timeout="10000" wadh=""  env="P" otp="" posh=""/>' + "</PidOptions>";
    /*
                format=\"0\"     --> XML
                format=\"1\"     --> Protobuf   env=\"P\"
                */
    var xhr;
    var ua = window.navigator.userAgent;
    var msie = ua.indexOf("MSIE ");

    if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./)) {
        // If Internet Explorer, return version number
        //IE browser
        xhr = new ActiveXObject("Microsoft.XMLHTTP");
    } else {
        //other browser
        xhr = new XMLHttpRequest();
    }

    xhr.open("CAPTURE", url, true);
    xhr.setRequestHeader("Content-Type", "text/xml");
    xhr.setRequestHeader("Accept", "text/xml");

    xhr.onload = function () {
        //if(xhr.readyState == 1 && count == 0){
        //  fakeCall();
        //}
        if (xhr.readyState == 4) {
            var status = xhr.status;
            $(".large-loader").css("display", "block");
            if (status == 200) {
                //console.log(xhr.responseText);
                //alert(xhr.responseText);
                //return false;
                Call2FaApi(xhr.responseText);
            } else {
                alert("Something went wrong ! Please try again later.");
                ClearCtrl();
            }
        }
    };

    xhr.send(PIDOPTS);
}

function Capture2FaMorphoApScan() {
    $(".large-loader").css("display", "block");
    //debugger;
    var url = "http://127.0.0.1:11100/capture";
    var PIDOPTS = '<PidOptions ver="1.0">' + '<Opts fCount="1" fType="2" iCount="" iType="" pCount="" pType="" format="0" pidVer="2.0" timeout="10000" wadh=""  env="P" otp="" posh=""/>' + "</PidOptions>";
    /*
                format=\"0\"     --> XML
                format=\"1\"     --> Protobuf   env=\"P\"
                */
    var xhr;
    var ua = window.navigator.userAgent;
    var msie = ua.indexOf("MSIE ");

    if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./)) {
        // If Internet Explorer, return version number
        //IE browser
        xhr = new ActiveXObject("Microsoft.XMLHTTP");
    } else {
        //other browser
        xhr = new XMLHttpRequest();
    }

    xhr.open("CAPTURE", url, true);
    xhr.setRequestHeader("Content-Type", "text/xml");
    xhr.setRequestHeader("Accept", "text/xml");

    xhr.onload = function () {
        //if(xhr.readyState == 1 && count == 0){
        //  fakeCall();
        //}
        if (xhr.readyState == 4) {
            var status = xhr.status;
            $(".large-loader").css("display", "block");
            if (status == 200) {
                //console.log(xhr.responseText);
                //alert(xhr.responseText);
                //return false;
                Call2FaApApi(xhr.responseText);
            } else {
                alert("Something went wrong ! Please try again later.");
                ClearCtrl();
            }
        }
    };

    xhr.send(PIDOPTS);
}

function discoverAvdm() {
    // New
    //
    var SuccessFlag = 0;
    var primaryUrl = "http://127.0.0.1:";

    try {
        var protocol = window.location.href;
        if (protocol.indexOf("https") >= 0) {
            primaryUrl = "http://127.0.0.1:";
        }
    } catch (e) { }

    url = "";
    //$("#ddlAVDM").empty();
    //alert("Please wait while discovering port from 11100 to 11120.\nThis will take some time.");
    for (var i = 11100; i <= 11120; i++) {
        if (primaryUrl == "https://127.0.0.1:" && OldPort == true) {
            i = "8005";
        }
        //$("#lblStatus1").text("Discovering RD service on port : " + i.toString());

        var verb = "RDSERVICE";
        var err = "";
        SuccessFlag = 0;
        var res;
        $.support.cors = true;
        var httpStaus = false;
        var jsonstr = "";
        var data = new Object();
        var obj = new Object();

        $.ajax({
            type: "RDSERVICE",
            async: false,
            crossDomain: true,
            url: primaryUrl + i.toString(),
            contentType: "text/xml; charset=utf-8",
            processData: false,
            cache: false,
            crossDomain: true,

            success: function (data) {
                httpStaus = true;
                res = { httpStaus: httpStaus, data: data };
                //alert(data);
                finalUrl = primaryUrl + i.toString();
                var $doc = $.parseXML(data);
                var CmbData1 = $($doc).find("RDService").attr("status");
                var CmbData2 = $($doc).find("RDService").attr("info");
                if (RegExp("\\b" + "Mantra" + "\\b").test(CmbData2) == true) {
                    if ($($doc).find("Interface").eq(0).attr("path") == "/rd/capture") {
                        MethodCapture = $($doc).find("Interface").eq(0).attr("path");
                    }
                    if ($($doc).find("Interface").eq(1).attr("path") == "/rd/capture") {
                        MethodCapture = $($doc).find("Interface").eq(1).attr("path");
                    }
                    if ($($doc).find("Interface").eq(0).attr("path") == "/rd/info") {
                        MethodInfo = $($doc).find("Interface").eq(0).attr("path");
                    }
                    if ($($doc).find("Interface").eq(1).attr("path") == "/rd/info") {
                        MethodInfo = $($doc).find("Interface").eq(1).attr("path");
                    }
                    SuccessFlag = 1;
                    return;
                }
            },
            error: function (jqXHR, ajaxOptions, thrownError) {
                if (i == "8005" && OldPort == true) {
                    OldPort = false;
                    i = "11099";
                }

                //alert(thrownError);

                //res = { httpStaus: httpStaus, err: getHttpError(jqXHR) };
            },
        });

        if (SuccessFlag == 1) {
            break;
        }

        //$("#ddlAVDM").val("0");
    }

    if (SuccessFlag == 0) {
        alert("Connection failed Please try again.");
        ClearCtrl();
    }

    //$("select#ddlAVDM").prop('selectedIndex', 0);

    //$('#txtDeviceInfo').val(DataXML);

    return res;
}

function CaptureAvdm() {
    //$('#Dv_Loader').show();

    discoverAvdm();
    if ($("#serviceType4").is(":checked")) {
        var XML = '<?xml version="1.0"?> <PidOptions ver="1.0"> <Opts fCount="1" fType="2" iCount="0" pCount="0" format="0"   pidVer="2.0" timeout="10000" posh="UNKNOWN" env="P" /> </PidOptions>';
    } else {
        var XML = '<?xml version="1.0"?> <PidOptions ver="1.0"> <Opts fCount="1" fType="2" iCount="0" pCount="0" format="0"   pidVer="2.0" timeout="10000" posh="UNKNOWN" env="P" /> </PidOptions>';
    }
    var verb = "CAPTURE";
    var err = "";
    var res;
    $.support.cors = true;
    var httpStaus = false;
    var jsonstr = "";

    $.ajax({
        type: "CAPTURE",
        async: false,
        crossDomain: true,
        url: finalUrl + MethodCapture,
        //  url: "http://127.0.0.1:11100/capture",
        data: XML,
        contentType: "text/xml; charset=utf-8",
        processData: false,
        success: function (data) {
            // alert(data);
            httpStaus = true;
            res = { httpStaus: httpStaus, data: data };
            //$('#txtPidData').val(data);
            //$('#txtPidOptions').val(XML);
            var $doc = $.parseXML(data);
            var Message = $($doc).find("Resp").attr("errInfo");
            var AAdharNumber = $("#aadhar_no").val();
            var Amount = $("#amount").val();
            var MobileNumber = $("#mobile").val();
            var IIN = $("#bankID").val();
            var deviceIMEI = "";
            var Service = GetServiceType();
            if (Service == "balinfo" || Service == "ministatement") {
                $(".large-loader").css("display", "none");
                if (Amount == 0) {
                    CallApi(Service, data);
                } else {
                    alert("Wrong Input Amount");
                    ClearCtrl();
                }
            } else if (Service == "balwithdraw" || Service == "aadharpay") {
                if (Amount <= 10000 && Amount >= 50) {
                    $(".large-loader").css("display", "none");
                    CallApi(Service, data);
                } else {
                    alert("Amount should be less than 10000 and grater than or equal 50.");
                    ClearCtrl();
                }
            }
        },
        error: function (jqXHR, ajaxOptions, thrownError) {
            //$('#txtPidOptions').val(XML);
            alert(thrownError);
            res = { httpStaus: httpStaus, err: getHttpError(jqXHR) };
        },
    });

    return res;
}

function CaptureKycAvdm() {
    //$('#Dv_Loader').show();

    discoverAvdm();
    //var XML = '<?xml version="1.0"?> <PidOptions ver="1.0"> <Opts fCount="1" fType="0" iCount="0" pCount="0" format="0"   pidVer="2.0" timeout="10000" posh="UNKNOWN" env="P" /> </PidOptions>';
    var XML =
        '<?xml version="1.0"?> <PidOptions ver="1.0"> <Opts fCount="1" fType="2" iCount="0" pCount="0" format="0" wadh="E0jzJ/P8UopUHAieZn8CKqS4WPMi5ZSYXgfnlfkWjrc="   pidVer="2.0" timeout="15000" posh="UNKNOWN" env="P" /> </PidOptions>';
    var verb = "CAPTURE";
    var err = "";
    var res;
    $.support.cors = true;
    var httpStaus = false;
    var jsonstr = "";

    $.ajax({
        type: "CAPTURE",
        async: false,
        crossDomain: true,
        url: finalUrl + MethodCapture,
        //  url: "http://127.0.0.1:11100/capture",
        data: XML,
        contentType: "text/xml; charset=utf-8",
        processData: false,
        success: function (data) {
            // alert(data);
            httpStaus = true;
            res = { httpStaus: httpStaus, data: data };
            //$('#txtPidData').val(data);
            //$('#txtPidOptions').val(XML);
            var $doc = $.parseXML(data);

            CallKYCVerifyApi(data);
        },
        error: function (jqXHR, ajaxOptions, thrownError) {
            //$('#txtPidOptions').val(XML);
            alert(thrownError);
            res = { httpStaus: httpStaus, err: getHttpError(jqXHR) };
        },
    });

    return res;
}

function Capture2FaKycAvdm() {
    //$('#Dv_Loader').show();

    discoverAvdm();
    //var XML = '<?xml version="1.0"?> <PidOptions ver="1.0"> <Opts fCount="1" fType="0" iCount="0" pCount="0" format="0"   pidVer="2.0" timeout="10000" posh="UNKNOWN" env="P" /> </PidOptions>';
    var XML = '<?xml version="1.0"?> <PidOptions ver="1.0"> <Opts fCount="1" fType="2" iCount="0" pCount="0" format="0" wadh=""   pidVer="2.0" timeout="15000" posh="UNKNOWN" env="P" /> </PidOptions>';
    var verb = "CAPTURE";
    var err = "";
    var res;
    $.support.cors = true;
    var httpStaus = false;
    var jsonstr = "";

    $.ajax({
        type: "CAPTURE",
        async: false,
        crossDomain: true,
        url: finalUrl + MethodCapture,
        //  url: "http://127.0.0.1:11100/capture",
        data: XML,
        contentType: "text/xml; charset=utf-8",
        processData: false,
        success: function (data) {
            // alert(data);
            httpStaus = true;
            res = { httpStaus: httpStaus, data: data };
            //$('#txtPidData').val(data);
            //$('#txtPidOptions').val(XML);
            var $doc = $.parseXML(data);

            Call2FaApi(data);
        },
        error: function (jqXHR, ajaxOptions, thrownError) {
            //$('#txtPidOptions').val(XML);
            alert(thrownError);
            res = { httpStaus: httpStaus, err: getHttpError(jqXHR) };
        },
    });

    return res;
}

// function Capture2FaStartekScan() {
//         alert('test');

//                 var port;
//          var urlStr = '';

//          urlStr = 'http://localhost:11100/rd/capture';

//         getJSONCapture(urlStr,
//          function (err, data) {
//          if (err != null) {
//          alert('Something went wrong: ' + err);
//          } else {
//          //alert('Response:-' + String(data);
//          }
//          }
//          );
//          }
//          var getJSONCapture = function (url, callback) {
//          var xhr = new XMLHttpRequest();
//          xhr.open('CAPTURE', url, true);
//         xhr.responseType = 'text'; //json
//         var InputXml = "<PidOptions> <Opts fCount\"1\" fType=\"0\" iCount=\"0\"
//         pCount=\"0\" format=\"0\" pidVer=\"2.0\" timeout=\"20000\" otp=\"\"
//         posh=\"UNKNOWN\" env=\"S\" wadh=\"\" /> <Demo></Demo> <CustOpts> <Param
//         name=\"ValidationKey\" value=\"\" /> </CustOpts> </PidOptions>";
//          xhr.onload = function () {
//          var status = xhr.status;
//          if (status == 200) {
//              alert(null, xhr.response);
//          callback(null, xhr.response);
//          } else {
//          callback(status);
//          }
//          };
//          xhr.send(InputXml);
//          };

// }

function Capture2FaStartekScan() {
    var urlStr = "http://localhost:11100/";

    getJSONCapture(urlStr, function (err, data) {
        if (err != null) {
            alert("Something went wrong: " + err);
        } else {
            alert("Response: " + String(data));
        }
    });
}

var getJSONCapture = function (url, callback) {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", url, true);
    xhr.responseType = "text"; // Set responseType to text for debugging, can change to 'json' if the response is in JSON

    // Correctly formatted XML string
    var InputXml = `
        <PidOptions>
            <Opts fCount="1" fType="0" iCount="0" pCount="0" format="0" pidVer="2.0" timeout="20000" otp="" posh="UNKNOWN" env="S" wadh="" />
            <Demo></Demo>
            <CustOpts>
                <Param name="ValidationKey" value="" />
            </CustOpts>
        </PidOptions>`;

    xhr.onload = function () {
        var status = xhr.status;
        if (status == 200) {
            callback(null, xhr.response);
        } else {
            callback(status);
        }
    };

    xhr.onerror = function () {
        callback("Request failed");
    };

    xhr.send(InputXml);
};

// function Capture2FaStartekScan() {
//     var urlStr = 'http://127.0.0.1:11100/capture';

//     getJSONCapture(urlStr, function (err, data) {
//         if (err != null) {
//             alert('Something went wrong: ' + err);
//         } else {
//             alert('Response: ' + String(data));
//         }
//     });
// }

// var getJSONCapture = function (url, callback) {
//       //$.support.cors = true;
//     /*var InputXml = `
//         <PidOptions>
//             <Opts fCount="1" fType="0" iCount="0" pCount="0" format="0" pidVer="2.0" timeout="20000" otp="" posh="UNKNOWN" env="S" wadh="" />
//             <Demo></Demo>
//             <CustOpts>
//                 <Param name="ValidationKey" value="" />
//             </CustOpts>
//         </PidOptions>`;
//             */

//             var InputXml = '<?xml version="1.0"?> <PidOptions ver="1.0"> <Opts fCount="1" fType="2" iCount="0" pCount="0" format="0" wadh=""   pidVer="2.0" timeout="15000" posh="UNKNOWN" env="P" /> </PidOptions>';
//     $.ajax({
//         url: url,
//         type: 'CAPTURE',
//          async: false,
//         crossDomain: true,
//         contentType: 'text/xml',
//         data: InputXml,
//         dataType: 'text', // Set to 'json' if the response is JSON
//         success: function (response) {
//             callback(null, response);
//         },
//         error: function (xhr, status, error) {
//             callback(status + ': ' + error);
//         }
//     });
// };

function Capture2FaKycApAvdm() {
    //$('#Dv_Loader').show();

    discoverAvdm();
    //var XML = '<?xml version="1.0"?> <PidOptions ver="1.0"> <Opts fCount="1" fType="0" iCount="0" pCount="0" format="0"   pidVer="2.0" timeout="10000" posh="UNKNOWN" env="P" /> </PidOptions>';
    var XML = '<?xml version="1.0"?> <PidOptions ver="1.0"> <Opts fCount="1" fType="2" iCount="0" pCount="0" format="0" wadh=""   pidVer="2.0" timeout="15000" posh="UNKNOWN" env="P" /> </PidOptions>';
    var verb = "CAPTURE";
    var err = "";
    var res;
    $.support.cors = true;
    var httpStaus = false;
    var jsonstr = "";

    $.ajax({
        type: "CAPTURE",
        async: false,
        crossDomain: true,
        url: finalUrl + MethodCapture,
        //  url: "http://127.0.0.1:11100/capture",
        data: XML,
        contentType: "text/xml; charset=utf-8",
        processData: false,
        success: function (data) {
            // alert(data);
            httpStaus = true;
            res = { httpStaus: httpStaus, data: data };
            //$('#txtPidData').val(data);
            //$('#txtPidOptions').val(XML);
            var $doc = $.parseXML(data);

            Call2FaApApi(data);
        },
        error: function (jqXHR, ajaxOptions, thrownError) {
            //$('#txtPidOptions').val(XML);
            alert(thrownError);
            res = { httpStaus: httpStaus, err: getHttpError(jqXHR) };
        },
    });

    return res;
}

//captcture mantra

function CallApi(ServiceType, BiometricData) {
    var siteUrl = $("#siteUrl").val();
    var Device = GetDevice();
    var Service = GetServiceType();
    var AAdharNumber = $("#aadhar_no").val();
    var Amount = $("#amount").val();
    var MobileNumber = $("#mobile").val();
    var IIN = $("#bankID").val();
    var deviceIMEI = "";
    var jsonData = { ServiceType: ServiceType, deviceIMEI: deviceIMEI, AadharNumber: AAdharNumber, mobileNumber: MobileNumber, BiometricData: BiometricData, Amount: Amount, IIN: IIN };
    jQuery.ajax({
        type: "POST",
        url: siteUrl + "retailer/fingpayAeps/apiAuth",
        contentType: "application/json; charset=utf-8",
        data: JSON.stringify(jsonData),
        dataType: "json",
        success: function (Result) {
            if (Result.status == 1) {
                if (Result.is_bal_info == 1) {
                    $(".aeps-response").html('<div class="aeps-response-right"><i class="fa fa-check" aria-hidden="true"></i></div><p>Account Balance - INR ' + Result.balanceAmount + "/- <br />" + Result.msg + "</p>");
                    $(".large-loader").css("display", "none");
                    $("#aepsResponseModal").modal("show");
                } else if (Result.is_withdrawal == 1) {
                    $(".aeps-response").html('<div class="aeps-response-right"><i class="fa fa-check" aria-hidden="true"></i></div><p>' + Result.msg + "</p>" + Result.str);
                    $(".large-loader").css("display", "none");
                    $("#aepsResponseModal").modal("show");
                } else {
                    $(".aeps-response").html('<div class="aeps-response-right"><i class="fa fa-check" aria-hidden="true"></i></div><p>Account Balance - INR ' + Result.balanceAmount + "/- <br />" + Result.msg + "</p>" + Result.str);
                    $(".large-loader").css("display", "none");
                    $("#aepsResponseModal").modal("show");
                }
            } else if (Result.is_daily_login == 1) {
                $(".large-loader").css("display", "none");
                swal({
                    title: Result["msg"],
                    icon: "error",
                    button: "OK!",
                });

                setTimeout(function () {
                    window.location.replace(siteUrl + "retailer/fingpayAeps/memberRegister");
                });
            } else {
                $(".aeps-response").html('<div class="aeps-response-error"><i class="fa fa-times" aria-hidden="true"></i></div><p>' + Result.msg + "</p>");
                $(".large-loader").css("display", "none");
                $("#aepsResponseModal").modal("show");
            }
        },
    });
}

function Call2FaApi(BiometricData) {
    var siteUrl = $("#siteUrl").val();
    var Device = GetDevice();
    var ServiceType = "2FAAuth";
    var AAdharNumber = $("#aadhar_no").val();
    var Amount = $("#amount").val();
    var MobileNumber = $("#mobile").val();
    // var IIN = $("#bankID").val();
    var deviceIMEI = "";
    var jsonData = { ServiceType: ServiceType, deviceIMEI: deviceIMEI, AadharNumber: AAdharNumber, mobileNumber: MobileNumber, BiometricData: BiometricData, Amount: Amount };
    jQuery.ajax({
        type: "POST",
        url: siteUrl + "retailer/fingpayAeps/api2FaAuth",
        contentType: "application/json; charset=utf-8",
        data: JSON.stringify(jsonData),
        dataType: "json",
        success: function (Result) {
            if (Result.status == 1) {
                $(".large-loader").css("display", "none");
                swal({
                    title: Result["msg"],
                    icon: "success",
                    button: "OK!",
                });

                setTimeout(function () {
                    window.location.replace(siteUrl + "retailer/fingpayAeps");
                });
            } else {
                $(".large-loader").css("display", "none");
                swal({
                    title: Result["msg"],
                    icon: "error",
                    button: "OK!",
                });
                setTimeout(function () {
                    window.location.replace(siteUrl + "retailer/fingpayAeps");
                });
            }
        },
    });
}

function Call2FaApApi(BiometricData) {
    var siteUrl = $("#siteUrl").val();
    var Device = GetDevice();
    var ServiceType = "2FAAuth";
    var AAdharNumber = $("#aadhar_no").val();
    var Amount = $("#amount").val();
    var MobileNumber = $("#mobile").val();
    // var IIN = $("#bankID").val();
    var deviceIMEI = "";
    var jsonData = { ServiceType: ServiceType, deviceIMEI: deviceIMEI, AadharNumber: AAdharNumber, mobileNumber: MobileNumber, BiometricData: BiometricData, Amount: Amount };
    jQuery.ajax({
        type: "POST",
        url: siteUrl + "retailer/fingpayAeps/api2FaAuthNew",
        contentType: "application/json; charset=utf-8",
        data: JSON.stringify(jsonData),
        dataType: "json",
        success: function (Result) {
            if (Result.status == 1) {
                $(".large-loader").css("display", "none");
                swal({
                    title: Result["msg"],
                    icon: "success",
                    button: "OK!",
                });

                setTimeout(function () {
                    window.location.replace(siteUrl + "retailer/fingpayAeps");
                });
            } else {
                $(".large-loader").css("display", "none");
                swal({
                    title: Result["msg"],
                    icon: "error",
                    button: "OK!",
                });
                setTimeout(function () {
                    window.location.replace(siteUrl + "retailer/fingpayAeps");
                });
            }
        },
    });
}

function CallKYCVerifyApi(BiometricData) {
    var siteUrl = $("#siteUrl").val();
    var encodeFPTxnId = $("#encodeFPTxnId").val();
    var jsonData = { encodeFPTxnId: encodeFPTxnId, BiometricData: BiometricData };
    jQuery.ajax({
        type: "POST",
        url: siteUrl + "retailer/fingpayAeps/kycBioAuth",
        contentType: "application/json; charset=utf-8",
        data: JSON.stringify(jsonData),
        dataType: "json",
        success: function (Result) {
            if (Result.status == 1) {
                window.location.href = siteUrl + "retailer/fingpayAeps";
                $(".aeps-response").html('<font color="green">' + Result.msg + "</font>");
                $(".large-loader").css("display", "none");
            } else {
                $(".aeps-response").html('<font color="red">' + Result.msg + "</font>");
                $(".large-loader").css("display", "none");
            }
        },
    });
}

function GetServiceType() {
    if ($("#serviceType1").prop("checked")) {
        return "balinfo";
    } else if ($("#serviceType2").prop("checked")) {
        return "ministatement";
    } else if ($("#serviceType3").prop("checked")) {
        return "balwithdraw";
    } else if ($("#serviceType4").prop("checked")) {
        return "aadharpay";
    }
    return false;
}

function GetDevice() {
    if ($("#deviceType1").prop("checked")) {
        return "MANTRA_PROTOBUF";
    } else if ($("#deviceType2").prop("checked")) {
        return "MORPHO_PROTOBUF";
    } else if ($("#deviceType3").prop("checked")) {
        return "STARTEK.ACPL";
    }
    return false;
}

function GetBankIin() {
    var rb = document.getElementById("<%=Rb_Bank.ClientID%>");
    var radio = rb.getElementsByTagName("input");
    var label = rb.getElementsByTagName("label");
    for (var i = 0; i < radio.length; i++) {
        if (radio[i].checked) {
            if (radio[i].value == 0) {
                var bank = document.getElementById("<%=Ddl_OtherBank.ClientID %>");
                return bank.value;
            } else {
                return radio[i].value;
            }
            break;
        }
    }
    return false;
}

function Print() {
    var panel = document.getElementById("Dv_Print");
    var printWindow = window.open("", "", "height=400,width=800");
    printWindow.document.write("<html><head><title>Cash Receipt</title>");
    printWindow.document.write("</head><body >");
    printWindow.document.write(panel.innerHTML);
    printWindow.document.write("</body></html>");
    printWindow.document.close();
    setTimeout(function () {
        printWindow.print();
        location.reload();
    }, 500);
    return false;
    location.reload();
}

function Print1() {
    var panel = document.getElementById("Dv_Print1");
    var printWindow = window.open("", "", "height=400,width=800");
    printWindow.document.write("<html><head><title>Cash Receipt</title>");
    printWindow.document.write("</head><body >");
    printWindow.document.write(panel.innerHTML);
    printWindow.document.write("</body></html>");
    printWindow.document.close();
    setTimeout(function () {
        printWindow.print();
        location.reload();
    }, 500);
    return false;
    location.reload();
}
function ClearCtrl() {
    location.reload();
}
