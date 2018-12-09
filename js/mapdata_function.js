function checkform() {

    var msg="";

    var company=document.getElementById('company').value;

    if(company=="")
        msg+="Company name cannot be blank \r\n\r\n";

    msg+=checkpercent();

    if(msg!=""){
        alert(msg);
        return false;
    }

    return true;
}

function checkpercent() {

    var keys=document.getElementById('key').value;
    var msg="";

    for (i = 0; i < keys; i++) {
        var cattot=0;
        var cat1val=Number(document.getElementById('cat1'+i).value);
        var cat2val=Number(document.getElementById('cat2'+i).value);
        var cat3val=Number(document.getElementById('cat3'+i).value);

        var cattot=cat1val+cat2val+cat3val;

        //alert(i+":::"+cattot);

        if(cattot!=100)
            msg+=document.getElementById('item'+i).value+"\r\n";
    }

    if(msg!="")
        msg="Total for the following items must add up to a 100 \r\n\r\n"+msg;

    return msg;
}

function populatefield(fld,key) {

    switch(fld) {
        case "cat1":
            if(Number(document.getElementById('cat2'+key).value)==100 || Number(document.getElementById('cat3'+key).value)==100){
                document.getElementById('cat1'+key).value='100';
                document.getElementById('cat2'+key).value='0';
                document.getElementById('cat3'+key).value='0';
            }
            break;
        case "cat2":
            if(Number(document.getElementById('cat1'+key).value)==100 || Number(document.getElementById('cat3'+key).value)==100){
                document.getElementById('cat1'+key).value='0';
                document.getElementById('cat2'+key).value='100';
                document.getElementById('cat3'+key).value='0';
            }
            break;
        case "cat3":
            if(Number(document.getElementById('cat1'+key).value)==100 || Number(document.getElementById('cat2'+key).value)==100){
                document.getElementById('cat1'+key).value='0';
                document.getElementById('cat2'+key).value='0';
                document.getElementById('cat3'+key).value='100';
            }
            break;
        default:
            document.getElementById('cat1'+key).value='100';
            document.getElementById('cat2'+key).value='0';
            document.getElementById('cat3'+key).value='0';
    }

}

function checkblank(ele) {
    if(ele.value==="")
        ele.value='0';
}

function openTab(evt, tabname) {
    // Declare all variables
    var i, tabcontent, tablinks;

    // Get all elements with class="tabcontent" and hide them
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }

    // Get all elements with class="tablinks" and remove the class "active"
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }

    // Show the current tab, and add an "active" class to the link that opened the tab
    document.getElementById(tabname).style.display = "block";
    evt.currentTarget.className += " active";
}