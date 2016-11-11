// jquery change navigation bar
// may be it can be embedded into angularjs?
$(document).ready(function(){
    
        // left top button
        var bodyEl = $('body'),
            navToggleBtn = bodyEl.find('.headerButton');
        navToggleBtn.on('click', function(event) {
//            console.log('navigate button on click');
            bodyEl.toggleClass('active-nav');
            event.preventDefault();
        });
    
        // activate carousel
//        $("#slideCarouselBtn").carousel();
});

// define aws url constant
var urlPrefix = "http://zp007app.edh2mtbzxn.us-west-2.elasticbeanstalk.com/";

// angularjs application initialization, reference dirPagination
var myApp = angular.module('myApp', ['angularUtils.directives.dirPagination', 'ui.bootstrap']);


// navigation bar controller, handle four kinds of click-request
function controller($scope, $http, $sce, $window) {
    
    // class member
    $scope.category = '';
    $scope.subordinateTabs = [];
    $scope.resArray = [];
    $scope.tabKeyword = '';
    $scope.currentPage = 1;
    $scope.itemsPerPage = 10;
    $scope.committeesJsonRes = [];
    $scope.billsJsonRes = [];
    $scope.b_cNum = 5; // view detail, number of items in the list
    $scope.currentLegislator = null;
    $scope.currentBill = null;
    $scope.pdfTrustSrc = '';
    $scope.fvResArray_legislators = [];
    $scope.fvResArray_bills = [];
    $scope.fvResArray_committees = [];
    $scope.starIsActive = false;
    
    
    // category -> tabs dictionary
    var dict = {};
    var legislatorsTabs = [
        {title: 'By State'}, 
        {title: 'House', disabled: true},
        {title: 'Senate', disabled: true}
    ];
    var billsTabs = [
        {title: 'Active Bills'},
        {title: 'New Bills', disabled: true},
    ];
    var committeesTabs = [
        {title: 'House'},
        {title: 'Senate', disabled: true},
        {title: 'Joint', disabled: true}
    ];
    var favoritesTabs = [
        {title: 'Legislators', active: true},
        {title: 'Bills', active: false, disabled: true},
        {title: 'Committees', active: false, disabled: true}
    ];
    
    dict['legislators'] = legislatorsTabs;
    dict['bills'] = billsTabs;
    dict['committees'] = committeesTabs;
    dict['favorites'] = favoritesTabs;
    
    
    // selected tab -> filter key word, an array
    var tabReflection = [];
    
    tabReflection["By State"] = "State";
    tabReflection["House"] = "house";
    tabReflection["Senate"] = "senate";
    tabReflection["Active Bills"] = "activeBills";
    tabReflection["New Bills"] = "newBills";
    tabReflection["Joint"] = "joint";
    tabReflection["Legislators"] = "legislators";
    tabReflection["Bills"] = "bills";
    tabReflection["Committees"] = "committees";
    
    // format date
    var dateMonth = ["", "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    
    
    
    // bill result arraya
    var billDeActiveArray = [];
    var billActiveArray = []
    
    
    
    // handler for clicking at one category
    $scope.categoryHandler = function(event) {
        var element = event.target.id;
        $scope.category = element;
        $scope.subordinateTabs = dict[element];
        if(element === 'favorites') {
            $scope.selectTabHandler();
        }
        else if(element === 'bills') {
            var realURLActive = "index.php?category=bills&active=true";
            var realURLDeActive = "index.php?category=bills&active=false";
            $scope.sendRequest(element, realURLActive);
            $scope.sendRequest(element, realURLDeActive);
        }
        else {
            var realURL = "index.php?category=" + element;
            $scope.sendRequest(element, realURL);
        }      
    }
    
    
    $scope.sendRequest = function(element, realURL){
        // urlPrefix will be used after aws deployment
        $http({
            method: 'GET',
            url: realURL
        }).then(function successCallback(response) {
            console.log(response.data);
            var jsonStr = $scope.getRealJsonStr(response.data); // returned json string contains php comments
            
            if(element === 'bills') { // bills, active or not, request twice
                if(realURL.indexOf("active=true") !== -1) {
                    billActiveArray = angular.fromJson(jsonStr).results;
                    for(var i = 0; i < billActiveArray.length; i++) {
                        $scope.resArray.push(billActiveArray[i]);
                    }
                }
                else if(realURL.indexOf("active=false") !== -1) {
                    billDeActiveArray = angular.fromJson(jsonStr).results;
                    for(var i = 0; i < billDeActiveArray.length; i++) {
                        $scope.resArray.push(billDeActiveArray[i]);
                    }
                }
            }
            else { // others request once
                $scope.resArray = angular.fromJson(jsonStr).results;
            }
            
            $scope.selectTabHandler();
//            console.log($scope.resArray);
//            $scope.displayResults();
        }, function errorCallback(response) {
            alert('no');
        });
    }
    
    
    // see which tab is active, return first active tab object
    $scope.active = function() {
        return $scope.subordinateTabs.filter(function(tab) {
           return tab.active; 
        })[0];
    }
    
    // tabs handler
    $scope.selectTabHandler = function() {
        var activeTabTitle = $scope.active().title;
        // filter keyword reflection
//        console.log("current active tab:  " + activeTabTitle);
        $scope.tabKeyword = tabReflection[activeTabTitle];
        if($window.sessionStorage.getItem($scope.tabKeyword) !== null) {
            switch ($scope.tabKeyword) {
                case 'legislators':
                    $scope.fvResArray_legislators = JSON.parse($window.sessionStorage.getItem($scope.tabKeyword)); 
                    break;
                case 'bills':
                    $scope.fvResArray_bills = JSON.parse($window.sessionStorage.getItem($scope.tabKeyword));
                    break;
                case 'committees':
                    $scope.fvResArray_committees = JSON.parse($window.sessionStorage.getItem($scope.tabKeyword));
                    break;
                default:
                    break;
            }
        }
    }
    
    
    $scope.percentage = function() {
        var pct = 0;
        if($scope.currentLegislator !== null) {
            pct = (2016 - parseInt($scope.currentLegislator.term_start)) / (parseInt($scope.currentLegislator.term_end) - parseInt($scope.currentLegislator.term_start)) * 100;
        }
//        console.log(pct | 0);
        return (pct | 0);
    }
    
    // delete php comment in json string
    $scope.getRealJsonStr = function(jsonStr) {
        if(jsonStr.indexOf("php") !== -1) { // returned json string contains php comments
            jsonStr = jsonStr.substr(12);
        }
        return jsonStr;
    }
    
    // view detail button send request, wtf, it should be arranged in a dependent controller
    $scope.viewDetailSendRequest = function(realURL){
        // urlPrefix will be used after aws deployment
        $http({
            method: 'GET',
            url: realURL
        }).then(function successCallback(response) {
            var jsonStr = $scope.getRealJsonStr(response.data);
            
            if(realURL.indexOf("committee") !== -1) {
                $scope.committeesJsonRes = angular.fromJson(jsonStr).results;
//                console.log("committee detail" + $scope.committeesJsonRes);
            }
            else if(realURL.indexOf("bill") !== -1) {
                // legislator -> bill
                if(realURL.indexOf("bioguideId") !== -1) {
                    $scope.billsJsonRes = angular.fromJson(jsonStr).results;
//                    console.log("legislator bill detail" + $scope.billsJsonRes);
                }
                // bill -> bill
                else if(realURL.indexOf("billId") !== -1) {
                    $scope.currentBill = angular.fromJson(jsonStr).results[0];
                    $scope.pdfTrustSrc = $sce.trustAs($sce.RESOURCE_URL, $scope.currentBill.last_version.urls.pdf);
                    console.log('pdf src:  ' + $scope.pdfTrustSrc);
//                    console.log("single bill detail" + $scope.currentBill);
//                    console.log("single bill_id" + $scope.currentBill.bill_id);
                }
            }
            else if(realURL.indexOf("legislator") !== -1) {
                // legislator -> legislator
                $scope.currentLegislator = angular.fromJson(jsonStr).results[0];
                console.log('current legislator:  ' + $scope.currentLegislator.bioguide_id);
            }
        }, function errorCallback(response) {
            alert('view details fail');
        });
    }
    
    // view detail --- legislator
    $scope.viewLegislatorDetail = function(bioguideId) {
        var urlCommittees = "index.php?category=committees&bioguideId=" + bioguideId;
        var urlBills = "index.php?category=bills&bioguideId=" + bioguideId;
        var urlLegislators = "index.php?category=legislators&bioguideId=" + bioguideId;
        $scope.viewDetailSendRequest(urlCommittees);
        $scope.viewDetailSendRequest(urlBills);
        $scope.viewDetailSendRequest(urlLegislators);
    }
    
    // view detail --- bill
    $scope.viewBillDetail = function(billId) {
        var urlBills = "index.php?category=bills&billId=" + billId;
        $scope.viewDetailSendRequest(urlBills);
    }
    
    // view detail button
    $scope.viewDetailHandler = function(event) {
        if($scope.category === 'legislators') {
            var bioguideId = event.target.id;
            console.log("bioguide_id:  " + bioguideId);
            $scope.viewLegislatorDetail(bioguideId);
            
        }
        else if($scope.category === 'bills') {
            var billId = event.target.id;
            console.log("bill_id:  " + billId);
            $scope.viewBillDetail(billId);
            
        }
        else if($scope.category === 'favorites') {
            if($scope.tabKeyword === 'legislators') {
                var bioguideId = event.target.id;
                console.log("favorite -> bioguide_id:  " + bioguideId);
                $scope.viewLegislatorDetail(bioguideId);
            }
            else if($scope.tabKeyword === 'bills'){
                var billId = event.target.id;
                console.log("favorite -> bill_id:  " + billId);
                $scope.viewBillDetail(billId);
            }
        }
        
    }
    
    // get the specific date format
    $scope.formatDate = function(dateString) {
        if(!!dateString) {
            var year = dateString.substr(0, 4);
        var month = parseInt(dateString.substr(5, 7));
        var day = parseInt(dateString.substr(8));
        return dateMonth[month] + " " + day + ", " + year;
        }
        return null;
    }
    
    // get current obj via resArray in memory
    $scope.getCurrentCommittee = function(committeeId) {
        for(var i = 0; i < $scope.resArray.length; i++) {
            if(committeeId === $scope.resArray[i].committee_id) {
                return $scope.resArray[i];
            }
        }
        return null;
    }
    
    // trash bin -> delete favorite straightly
    $scope.deleteFavoriteHandler = function(categoryName, event) {
        var id = event.target.id;
        var fvArray = JSON.parse($window.sessionStorage.getItem(categoryName));
        var idx = -1;
        for(var i = 0; i < fvArray.length; i++) {
            switch (categoryName) {
                    case 'legislators':
                        if(fvArray[i].bioguide_id === id) {
                            idx = i;
                        }
                        break;
                    case 'bills':
                        if(fvArray[i].bill_id === id) {
                            idx = i;
//                            break;
                        }
                        break;
                    case 'committees':
                        if(fvArray[i].committee_id === id) {
                            idx = i;
//                            break;
                        }
                        break;
                    default:
                        break;
            }
        }
        // delete in array
        fvArray.splice(idx, 1);
        // update session storage
        $window.sessionStorage.setItem(categoryName, JSON.stringify(fvArray));
        $scope.selectTabHandler();
    }
    
    // add to favorite, if click twice, delete in favorite list
    $scope.favoriteHandler = function(categoryName, event) {
        $scope.starIsActive = !$scope.starIsActive;
        console.log('favorite category:  ' + categoryName);
        var fvCategory = categoryName;
        var fvId = event.target.id;
        
        var pushObj = {};
        switch (fvCategory) {
            case 'legislators':
                pushObj = $scope.currentLegislator;
                break;
            case 'bills':
                pushObj = $scope.currentBill;
                break;
            case 'committees':
                pushObj = $scope.getCurrentCommittee(fvId);
                break;
            default:
                break;
        }    
        console.log(pushObj);
        
        var fvArray = $window.sessionStorage.getItem(fvCategory);
        
        if(fvArray === null || fvArray ==="[null]") { // add
            fvArray = [];
            fvArray.push(pushObj);
            $window.sessionStorage.setItem(fvCategory, JSON.stringify(fvArray));
        }
        else { // exist -> delete; none -> add
            fvArray = JSON.parse($window.sessionStorage.getItem(fvCategory));
            var idx = -1;
            for(var i = 0; i < fvArray.length; i++) {
                switch (fvCategory) {
                    case 'legislators':
                        if(fvArray[i].bioguide_id === pushObj.bioguide_id) {
                            idx = i;
//                            break;
                        }
                        break;
                    case 'bills':
                        if(fvArray[i].bill_id === pushObj.bill_id) {
                            idx = i;
//                            break;
                        }
                        break;
                    case 'committees':
                        if(fvArray[i].committee_id === pushObj.committee_id) {
                            idx = i;
//                            break;
                        }
                        break;
                    default:
                        break;
                }
                if(idx > 0) {
                    break;
                }
            }
            
            if(idx === -1) { // new favorite
                fvArray.push(pushObj);
            }
            else { // click twice, delete record
                fvArray.splice(idx, 1);
            }
            
            // update session storage
            $window.sessionStorage.setItem(fvCategory, JSON.stringify(fvArray));
        }
        
    }
    
}

function pageDirController($scope) {
  $scope.pageChangeHandler = function(num) {
    console.log('going to page ' + num);
  };
}


myApp.controller('controller', controller);
myApp.controller('pageDirController', pageDirController);


//// service layer, shared properties
//myApp.service('sharedProperties', function() { 
//    
//    var currentLegislator = {};
//    
//    return {
//        getCurrentLegislator: function() {
//            return currentLegislator;
//        },
//        setCurrentLegislator: function(cl) {
//            currentLegislator = cl;
//        }   
//    };
//    
//});