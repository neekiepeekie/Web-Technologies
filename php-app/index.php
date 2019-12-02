<?php
if (isset($_GET['searchCall'])) {
    $API_KEY            = "DivyaRam-WebtechH-PRD-916e2f5cf-ae13ed25";
    $keyword            = rawurlencode($_GET['keyword']);
    $category           = $_GET['category'];
    $enableNearbySearch = $_GET['enableNearBySearch'];
    $maxDistance        = $_GET['maxDistance'];
    $zipcode            = $_GET['zipcode'];
    $condition          = $_GET['condition'];
    $shipping           = $_GET['shipping'];
    $ebay_api           = "http://svcs.ebay.com/services/search/FindingService/v1?OPERATION-NAME=findItemsAdvanced&SERVICE-VERSION=1.0.0&SECURITY-APPNAME=" . $API_KEY . "&RESPONSE-DATA-FORMAT=JSON&REST-PAYLOAD&paginationInput.entriesPerPage=20&keywords=" . $keyword;
    if (!empty($category)) {
        $ebay_api .= "&categoryId=" . $category;
    }
    $index = 0;
    if ($enableNearbySearch == "true") {
        $ebay_api .= "&buyerPostalCode=" . $zipcode . "&itemFilter(" . $index . ").name=MaxDistance&itemFilter(" . $index . ").value=" . $maxDistance;
        $index += 1;
    }
    if (!empty($condition)) {
        $condition = explode(',', $condition);
        $ebay_api .= "&itemFilter(" . $index . ").name=Condition";
        for ($x = 0; $x < count($condition); $x++) {
            $ebay_api .= "&itemFilter(" . $index . ").value(" . $x . ")=" . $condition[$x];
        }
        $index += 1;
    }
    if (!empty($shipping)) {
        $shipping = explode(',', $shipping);
        for ($x = 0; $x < count($shipping); $x++) {
            $ebay_api .= "&itemFilter(" . $index . ").name=" . $shipping[$x] . "&itemFilter(" . $index . ").value=true";
            $index += 1;
        }
    }
    $ebay_api .= "&itemFilter(" . $index . ").name=HideDuplicateItems&itemFilter(" . $index . ").value=true";
    $jsonobj = file_get_contents($ebay_api);
    echo $jsonobj;
    die();
}
if (isset($_GET['productCall'])) {
    $API_KEY  = "DivyaRam-WebtechH-PRD-916e2f5cf-ae13ed25";
    $ebay_api = "http://open.api.ebay.com/shopping?callname=GetSingleItem&responseencoding=JSON&appid=" . $API_KEY . "&siteid=0&version=967&ItemID=" . $_GET['itemId'] . "&IncludeSelector=Description,Details,ItemSpecifics";
    $jsonobj  = file_get_contents($ebay_api);
    echo $jsonobj;
    die();
}
if (isset($_GET['similarCall'])) {
    $API_KEY  = "DivyaRam-WebtechH-PRD-916e2f5cf-ae13ed25";
    $ebay_api = "http://svcs.ebay.com/MerchandisingService?OPERATION-NAME=getSimilarItems&SERVICE-NAME=MerchandisingService&SERVICE-VERSION=1.1.0&CONSUMER-ID=" . $API_KEY . "&RESPONSE-DATA-FORMAT=JSON&REST-PAYLOAD&itemId=" . $_GET['itemId'] . "&maxResults=8";
    $jsonobj  = file_get_contents($ebay_api);
    echo $jsonobj;
    die();
}
?>

<html>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<head>
    <title>Product Search</title>
    <style type="text/css">
        body {
            margin: 0;
        }
        
        .form-div {
            background-color: rgb(243, 243, 243);
            width: 700px;
            margin: 0 auto;
            border: 2px solid rgb(210, 210, 210);
            margin-top: 20px;
        }
        
        hr {
            color: rgb(243, 243, 243);
            opacity: 1;
        }
        
        #locationRadio {
            margin-left: 34.2em;
        }
        
        #searchOutput {
            padding-top: 20px;
        }
        
        #searchOutput table {
            border-collapse: collapse;
        }
        
        #searchOutput .products-table {
            width: 1450px
        }
        
        #searchOutput td {
            border: 2px solid #d1d1d1;
            text-align: left;
        }
        
        #searchOutput .products-table th {
            border: 2px solid #d1d1d1;
            text-align: center;
            height: 37px;
        }
        
        #searchOutput .item-table th,
        #searchOutput .item-table td {
            border: 2px solid #d1d1d1;
            text-align: left;
            padding: 0px 12px 0px 12px;
        }
        
        #searchOutput .products-table a,
        #similarTable a {
            text-decoration: none;
            color: black;
        }
        
        #searchOutput .products-table a:hover,
        #similarTable a:hover {
            color: darkgrey;
        }
        
        #sellerImg,
        #similarImg {
            width: 50px;
            display: block;
            margin: 0 auto;
            height: 25px;
            padding-bottom: 10px;
        }
        
        #sellerId,
        #similarId {
            text-align: center;
            color: #7b7979;
        }
        
        #iframeId {
            width: 1300px;
            border: none;
            overflow: hidden;
        }
        
        #sellerFrameId,
        #similarTableId {
            display: none;
        }
        
        #similarTable td {
            text-align: center;
        }
        
        #similarTableId {
            margin: auto;
        }
    </style>
    <script type="application/javascript">
        responseZip = 90007;
        function getGeoLoc() {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (this.status == 200 && this.readyState == 4) {
                    var response = JSON.parse(this.responseText);
                    responseZip = response.zip;
                    var button = document.getElementById("submit");
                    button.disabled = false;
                }
            }
            xmlhttp.open("GET", "http://ip-api.com/json", false);
            xmlhttp.send();
        }
        function updateCheckboxes(name, value) {
            var x = document.getElementsByName(name);
            var i;
            for (i = 0; i < x.length; i++) {
                x[i].checked = value;
            }
        }
        function clearAll() {
            document.getElementById("keyword").value = "";
            document.getElementById("category").value = "";
            updateCheckboxes("condition", false);
            updateCheckboxes("shipping", false);
            document.getElementById("radius").value = "";
            document.getElementById("radius").disabled = true;
            document.getElementById("radius").placeholder = 10;
            document.getElementById("here").checked = true;
            document.getElementById("here").disabled = true;
            document.getElementById("locationRadio").disabled = true;
            document.getElementById("location").disabled = true;
            document.getElementById("location").placeholder = "zip code";
            document.getElementById("location").value = "";
            updateCheckboxes("nearbySearch", false);
            document.getElementById("searchOutput").innerHTML = "";
            document.getElementById("sellerMessage").innerHTML = "";
            document.getElementById("similarItems").innerHTML = "";
            document.getElementById("sellerFrameId").style.display = "none";
        }
        function enableSearch(cb) {
            if (cb.checked) {
                document.getElementById("radius").disabled = false;
                document.getElementById("here").checked = true;
                document.getElementById("here").disabled = false;
                document.getElementById("locationRadio").disabled = false;
            } else {
                document.getElementById("radius").disabled = true;
                document.getElementById("here").disabled = true;
                document.getElementById("locationRadio").disabled = true;
                document.getElementById("location").disabled = true;
            }
        }
        function updateZipCode(updateTrue) {
            if (updateTrue) {
                document.getElementById("location").disabled = false;
            } else {
                document.getElementById("location").disabled = true;
            }
        }
        function showErrorMessage(message) {
            var searchOutputDiv = document.getElementById("searchOutput");
            searchOutputDiv.innerHTML = "";
            var div = document.createElement("div");
            div.style.width = "900px";
            div.style.textAlign = "center";
            div.style.backgroundColor = "rgb(243, 243, 243)";
            div.style.border = "2px solid rgb(210, 210, 210)";
            div.style.margin = "auto";
            div.innerHTML = message;
            searchOutputDiv.appendChild(div);
            document.getElementById("sellerMessage").innerHTML = "";
            document.getElementById("similarItems").innerHTML = "";
            document.getElementById("sellerFrameId").style.display = "none";
        }
        function getCheckBoxValues(name) {
            var result = []
            var cboxes = document.getElementsByName(name);
            var len = cboxes.length;
            for (var i = 0; i < len; i++) {
                if (cboxes[i].checked) {
                    result.push(cboxes[i].value);
                }
            }
            return result;
        }
        function getSearchUsefulFields(jsonobj) {
            var result = {};
            result['status'] = jsonobj.findItemsAdvancedResponse[0].ack[0].toLowerCase();
            if (jsonobj.findItemsAdvancedResponse[0].errorMessage) {
                result['errorMessage'] = jsonobj.findItemsAdvancedResponse[0].errorMessage[0].error[0].message;
            } else {
                var itemsCount = jsonobj.findItemsAdvancedResponse[0].searchResult[0]['@count'];
                if (itemsCount == "0") {
                    result['status'] = "failure";
                    result['errorMessage'] = "No Records has been found";
                } else {
                    var items = jsonobj.findItemsAdvancedResponse[0].searchResult[0].item;
                    var productArray = [];
                    for (index in items) {
                        var item = items[index];
                        var photo = "N/A";
                        if (item.galleryURL[0]) {
                            photo = "<img src='" + item.galleryURL[0] + "' style='max-width:100px;max-height:70px;'/>";
                        }
                        var name = "N/A";
                        if (item.title) {
                            name = item.title;
                        }
                        var price = "N/A";
                        if (item.sellingStatus && item.sellingStatus[0].currentPrice) {
                            price = item.sellingStatus[0].currentPrice[0].__value__;
                        }
                        var zipcode = "N/A";
                        if (item.postalCode) {
                            zipcode = item.postalCode;
                        }
                        var condition = "N/A";
                        if (item.condition) {
                            condition = item.condition[0].conditionDisplayName;
                        }
                        var shipping = "N/A";
                        if (item.shippingInfo && item.shippingInfo[0].shippingServiceCost) {
                            shippingCost = item.shippingInfo[0].shippingServiceCost[0].__value__;
                            if (shippingCost == "0.0") {
                                shipping = "Free Shipping";
                            } else {
                                shipping = "$" + shippingCost;
                            }
                        }
                        var productObj = {};
                        productObj['photo'] = photo;
                        productObj['id'] = item.itemId[0];
                        productObj['name'] = name;
                        productObj['price'] = price;
                        productObj['zipcode'] = zipcode;
                        productObj['condition'] = condition;
                        productObj['shipping'] = shipping;
                        productArray.push(productObj);
                    }
                    result['products'] = productArray;
                }
            }
            return result;
        }
        function getProductUsefulFields(jsonObj) {
            var result = {};
            if (jsonObj.Ack == "Success") {
                jsonObj = jsonObj.Item;
                result["status"] = "success";
                if (jsonObj.PictureURL && jsonObj.PictureURL.length > 0) {
                    result["Photo"] = jsonObj.PictureURL[0];
                }
                if (jsonObj.Title) {
                    result["Title"] = jsonObj.Title;
                }
                if (jsonObj.Subtitle) {
                    result["SubTitle"] = jsonObj.Subtitle;
                }
                if (jsonObj.CurrentPrice && jsonObj.CurrentPrice.Value) {
                    result["Price"] = jsonObj.CurrentPrice.Value + " " + jsonObj.CurrentPrice.CurrencyID;
                }
                if (jsonObj.Location) {
                    var locationStr = jsonObj.Location;
                    if (jsonObj.PostalCode) {
                        locationStr += ", " + jsonObj.PostalCode;
                    }
                    result["Location"] = locationStr;
                }
                if (jsonObj.Seller && jsonObj.Seller.UserID) {
                    result["Seller"] = jsonObj.Seller.UserID;
                }
                if (jsonObj.ReturnPolicy && jsonObj.ReturnPolicy.ReturnsAccepted) {
                    var returnStr = jsonObj.ReturnPolicy.ReturnsAccepted;
                    if (returnStr != "ReturnsNotAccepted") {
                        returnStr += " within " + jsonObj.ReturnPolicy.ReturnsWithin;
                    }
                    result["Return Policy (US)"] = returnStr;
                }
                if (jsonObj.ItemSpecifics && jsonObj.ItemSpecifics.NameValueList) {
                    var itemSpecifics = jsonObj.ItemSpecifics.NameValueList;
                    for (var index in itemSpecifics) {
                        var specific = itemSpecifics[index];
                        result[specific.Name] = specific.Value[0];
                    }
                } else {
                    result["No Detail Info from Seller"] = "";
                }
                if (jsonObj.Description && jsonObj.Description != "") {
                    result["Description"] = jsonObj.Description;
                }
            } else {
                result["status"] = "failure";
                result["message"] = jsonObj.Errors[0].LongMessage;
            }
            return result;
        }
        function serverSearchCall() {
            var searchForm = document.getElementById("searchForm");
            var zipcodeError = false;
            if (searchForm.nearbySearch.checked && document.getElementById("locationRadio").checked) {
                var zipcode = searchForm.location.value;
                if (!/^([0-9]{5})$/.test(zipcode)) {
                    zipcodeError = true;
                    showErrorMessage("Zipcode is invalid");
                }
            }
            if (!zipcodeError) {
                var keyword = encodeURI(searchForm.keyword.value);
                var categorySelect = document.getElementById("category");
                var category = categorySelect.options[categorySelect.selectedIndex].value;
                var condition = getCheckBoxValues("condition");
                var shipping = getCheckBoxValues("shipping");
                var enableNearBySearch = searchForm.nearbySearch.checked;
                var url = "index.php?searchCall=true&keyword=" + keyword + "&category=" + category + "&enableNearBySearch=" + enableNearBySearch;
                if (enableNearBySearch) {
                    var maxDistance = document.getElementById("radius").value;
                    if (maxDistance == "" || maxDistance == undefined || maxDistance == null) {
                        maxDistance = 10;
                    }
                    url += "&maxDistance=" + maxDistance;
                    if (document.getElementById("here").checked) {
                        url += "&zipcode=" + responseZip;
                    } else if (document.getElementById("locationRadio").checked) {
                        url += "&zipcode=" + document.getElementById("location").value;
                    }
                }
                url += "&condition=" + condition + "&shipping=" + shipping;
                var xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        var response = JSON.parse(this.responseText);
                        response = getSearchUsefulFields(response);
                        if (response.status === "failure") {
                            showErrorMessage(response.errorMessage);
                        } else {
                            var items = response.products;
                            tableHtml = "<table class='products-table' align='center' style='margin-top: 30px;'><tr><th>Index</th><th>Photo</th><th>Name</th><th>Price</th><th>Zip code</th><th>Condition</th><th>Shipping Option</th></tr>"
                            for (index in items) {
                                item = items[index];
                                itemId = parseInt(index) + 1;
                                tableHtml += "<tr>";
                                tableHtml += "<td>" + itemId + "</td>";
                                tableHtml += "<td style='text-align:center;width:100px;height: 70px;'>" + item.photo + "</td>";
                                tableHtml += "<td><a href='javascript:getProductDetails(" + item.id + ");'>" + item.name + "</a></td>";
                                tableHtml += "<td>$" + item.price + "</td>";
                                tableHtml += "<td>" + item.zipcode + "</td>";
                                tableHtml += "<td>" + item.condition + "</td>";
                                tableHtml += "<td>" + item.shipping + "</td>";
                                tableHtml += "</tr>"
                            }
                            document.getElementById("searchOutput").innerHTML = tableHtml;
                            document.getElementById("sellerMessage").innerHTML = "";
                            document.getElementById("sellerFrameId").style.display = "none";
                            document.getElementById("similarItems").innerHTML = "";
                        }
                    }
                }
                xhr.open("GET", url, true);
                xhr.send();
            }
        }
        function getProductDetails(itemId) {
            var url = "index.php?productCall=true&itemId=" + itemId;
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("sellerMessage").innerHTML = "";
                    document.getElementById("similarItems").innerHTML = "";
                    document.getElementById("sellerFrameId").style.display = "none";
                    var response = JSON.parse(this.responseText);
                    response = getProductUsefulFields(response);
                    if (response && response.status == "success") {
                        tableHtml = "<h1 style='text-align:center;margin-bottom:0px;'>Item Details</h1>"
                        tableHtml += "<table id='" + itemId + "' align='center' class='item-table'>";
                        for (var key in response) {
                            if (key != "Description" && key != "status" && key != "No Detail Info from Seller") {
                                tableHtml += "<tr>";
                                tableHtml += "<th>" + key + "</th>";
                                if (key == "Photo") {
                                    tableHtml += "<td><img style='width:250px;height:250px;' src='" + response[key] + "'/></td>";
                                } else {
                                    tableHtml += "<td>" + response[key] + "</td>";
                                }
                                tableHtml += "</tr>";
                            } else if (key == "No Detail Info from Seller") {
                                tableHtml += "<tr>";
                                tableHtml += "<th>" + key + "</th>";
                                tableHtml += "<td bgcolor='#c3c3c3'></td>";
                            }
                        }
                        document.getElementById("searchOutput").innerHTML = tableHtml;
                        var sellerMessage = "<p id='sellerId'>click to show seller message</p>";
                        sellerMessage += "<img src='http://csci571.com/hw/hw6/images/arrow_down.png' id='sellerImg' onclick='changeSellerMessage()'/>";
                        document.getElementById("sellerMessage").innerHTML = sellerMessage;
                        if (response.Description) {
                            document.getElementById("iframeId").setAttribute("srcdoc", response.Description);
                        } else {
                            var htmlContent = "<h4 style='width:900px;text-align:center;background-color:#c3c3c3;margin:auto'>No Seller Message found.</h4>"
                            document.getElementById("iframeId").setAttribute("srcdoc", htmlContent);
                        }
                        var similarItems = "<p id='similarId'>click to show similar items</p>";
                        similarItems += "<img src='http://csci571.com/hw/hw6/images/arrow_down.png' id='similarImg' onclick='changeSimilarItems()'/>";
                        similarItems += "<div id='similarTableId'></div>";
                        document.getElementById("similarItems").innerHTML = similarItems;
                    } else {
                        showErrorMessage(response.message);
                    }
                }
            }
            xhr.open("GET", url, true);
            xhr.send();
        }
        function changeSellerMessage() {
            var sellerImg = document.getElementById("sellerImg");
            if (sellerImg.src == "http://csci571.com/hw/hw6/images/arrow_down.png") {
                document.getElementById("sellerId").innerHTML = "click to hide seller message";
                sellerImg.setAttribute("src", "http://csci571.com/hw/hw6/images/arrow_up.png");
                document.getElementById("sellerFrameId").style.display = "block";
                var iframeHeight = document.getElementById("iframeId").contentWindow.document.body.scrollHeight + 20;
                document.getElementById("iframeId").style.height = iframeHeight + 'px';
                document.getElementById("similarId").innerHTML = "click to show similar items";
                document.getElementById("similarImg").setAttribute("src", "http://csci571.com/hw/hw6/images/arrow_down.png");
                document.getElementById("similarTableId").style.display = "none";
            } else {
                document.getElementById("sellerId").innerHTML = "click to show seller message";
                sellerImg.setAttribute("src", "http://csci571.com/hw/hw6/images/arrow_down.png");
                document.getElementById("sellerFrameId").style.display = "none";
            }
        }
        function getSimilarUsefulFields(jsonObj) {
            var items = jsonObj.getSimilarItemsResponse.itemRecommendations.item;
            var output = []
            for (index in items) {
                var item = items[index];
                var photo = "N/A";
                if (item.imageURL) {
                    photo = item.imageURL;
                }
                var title = "N/A";
                if (item.title) {
                    title = item.title;
                }
                var price = "N/A";
                if (item.buyItNowPrice && item.buyItNowPrice.__value__) {
                    price = item.buyItNowPrice.__value__;
                }
                var similarItem = {};
                similarItem.photo = photo;
                similarItem.title = title;
                similarItem.price = price;
                similarItem.itemId = item.itemId;
                output.push(similarItem);
            }
            return output;
        }
        function loadSimilarTable() {
            var itemId = document.getElementById("searchOutput").getElementsByTagName("table")[0].id;
            var container = document.getElementById("similarTableId");
            var similarItemsUrl = "index.php?similarCall=true&itemId=" + itemId;
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    var response = JSON.parse(this.responseText);
                    response = getSimilarUsefulFields(response);
                    if (response && response.length > 0) {
                        tableHtml = "<table id='similarTable' align='center' cellspacing='10'>";
                        tableHtml += "<tr>";
                        for (var index in response) {
                            var item = response[index];
                            tableHtml += "<td style='padding: 0px 40px 0px 40px;'><img src='" + item.photo + "' style='max-width:200px;max-height:200px;'/><br/><br/><a href='javascript:getProductDetails(" + item.itemId + ");'>" + item.title + "</a></td>";
                        }
                        tableHtml += "</tr><tr>";
                        for (var index in response) {
                            var item = response[index];
                            tableHtml += "<th>$" + item.price + "</th>";
                        }
                        tableHtml += "</tr>";
                        var similarTableId = document.getElementById("similarTableId");
                        similarTableId.innerHTML = tableHtml;
                        similarTableId.style.width = "900px";
                        similarTableId.style.overflow = "scroll";
                        similarTableId.style.border = "2px solid #bdbcbc";
                        similarTableId.style.padding = "10px";
                        similarTableId.style.margin = "auto";
                    } else {
                        var similarTableId = document.getElementById("similarTableId");
                        similarTableId.style.width = "900px";
                        similarTableId.style.borderStyle = "double";
                        similarTableId.style.borderWidth = "10px";
                        similarTableId.style.borderColor = "#bdbcbc";
                        similarTableId.innerHTML = "<h4 style='text-align:center;margin:0;'>No Similar Item found.</h4>";
                    }
                }
            }
            xhr.open("GET", similarItemsUrl, true);
            xhr.send();
        }
        function changeSimilarItems() {
            var sellerImg = document.getElementById("similarImg");
            if (sellerImg.src == "http://csci571.com/hw/hw6/images/arrow_down.png") {
                document.getElementById("similarId").innerHTML = "click to hide similar items";
                sellerImg.setAttribute("src", "http://csci571.com/hw/hw6/images/arrow_up.png");
                document.getElementById("similarTableId").style.display = "block";
                if (!document.getElementById("similarTable")) {
                    loadSimilarTable()
                }
                document.getElementById("sellerId").innerHTML = "click to show sellar message";
                document.getElementById("sellerImg").setAttribute("src", "http://csci571.com/hw/hw6/images/arrow_down.png");
                document.getElementById("sellerFrameId").style.display = "none";
            } else {
                document.getElementById("similarId").innerHTML = "click to show similar items";
                sellerImg.setAttribute("src", "http://csci571.com/hw/hw6/images/arrow_down.png");
                document.getElementById("similarTableId").style.display = "none";
            }
        }
    </script>
</head>

<body onload="getGeoLoc();">
    <div class="form-div">
        <form id="searchForm" action="JavaScript:serverSearchCall()" style="padding-left:15px;padding-right:15px;">
            <br>
            <p style="text-align: center;margin:0px; font-size:40px;"><i>Product Search</i></p>
            <hr>
            <p>
                <label><b>&nbsp;Keyword</b></label>
                <input type="text" id="keyword" name="keyword" required>
            </p>
            <p>
                <label><b>&nbsp;Category</b></label>
                <select id="category" name="category">
                    <option value="">All Categories</option>
                    <option value="550">Art</option>
                    <option value="2984">Baby</option>
                    <option value="267">Books</option>
                    <option value="11450">Clothing, Shoes & Accessories</option>
                    <option value="58058">Computers/Tablets & Networking</option>
                    <option value="26395">Health & Beauty</option>
                    <option value="11233">Music</option>
                    <option value="1249">Video Games & Consoles</option>
                </select>
            </p>

            <p>
                <label><b>&nbsp;Condition</b></label>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <input type="checkbox" name="condition" value="New">New&nbsp;&nbsp;
                <input type="checkbox" name="condition" value="Used">Used&nbsp;&nbsp;
                <input type="checkbox" name="condition" value="Unspecified">Unspecified&nbsp;&nbsp;
            </p>

            <p>
                <label><b>&nbsp;Shipping Options</b></label>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <input type="checkbox" name="shipping" value="LocalPickupOnly">Local Pickup&nbsp;&nbsp;
                <input type="checkbox" name="shipping" value="FreeShippingOnly">Free Shipping&nbsp;&nbsp;
            </p>

            <p>
                <input type="checkbox" name="nearbySearch" value="nearbySearch" onclick="enableSearch(this)">
                <label><b>&nbsp;Enable Nearby Search</b></label>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="text" id="radius" placeholder="10" size="7" disabled/>
                <label><b>miles from</b></label>&nbsp;&nbsp;&nbsp;
                <input onclick="updateZipCode(false)" type="radio" name="loc" id="here" value="here" disabled checked/> Here
                <br>
                <input onclick="updateZipCode(true)" type="radio" name="loc" id="locationRadio" value="locationRadio" disabled>
                <input type="text" id="location" placeholder="zip code" name="location" disabled required>
            </p>
            <p style="text-align:center;">
                <input id="submit" type="submit" name="submit" value="Search" disabled/>
                <input type="button" value="Clear" onclick="clearAll()">
            </p>

        </form>
    </div>
    <div id="searchOutput"></div>
    <div id="sellerMessageDiv">
        <div id="sellerMessage"></div>
        <div align='center' id='sellerFrameId'>
            <iframe frameborder='0' id='iframeId' srcdoc='' scrolling='no'></iframe>
        </div>
    </div>
    <div id="similarItems" style='padding-bottom:50px;'></div>
</body>

</html>