function nabidkafaktur(zakaznikID) {

    var presenter = $("#FakturaVydanaNezaplacena");

    var selector = "firma='" + zakaznikID + "' and stavUhrK !='stavUhr.uhrazeno'";
//    var selector = "%28firma%20=%20%27" + zakaznikID +"%27%29.json?q=filter[filterStavUhrK]=filterStavUhr.neuhraz";

    $.getJSON("flexiproxy.php?agenda=faktura-vydana&request=" + encodeURIComponent(selector), function (data) {



//https://flexibee.spoje.net:5434/c/spoje_net_s_r_o_/faktura-vydana/        
//faktura-vydana[filterStavUhrK]=filterStavUhr.neuhraz        

        if (data.length) {

            var items = [];
            $.each(data, function (key, faktura) {
                items.push("<div class='checkbox' id='" + key + "'><label><input onChange='updateCashForm()' data-value='" + faktura.zbyvaUhradit + "' class='invoice2settle' type='checkbox' name='fakturaID[" + faktura.id + "]' value='" + faktura.zbyvaUhradit + "' data-kod='" + faktura.kod + "'>" + faktura.kod + " <strong>" + faktura.zbyvaUhradit + '</strong><br>' + faktura.popis + "</label></div>");
            });

            $("<div>", {
                "class": "well",
                html: items.join("")
            }).appendTo(presenter);
        } else {
            presenter.html("Bez nezaplacených faktur");
        }
    });

    return true;
}

function updateCashForm(){
    $("#Celkem").val( countAllChecked() );
    $("#Popis").val( updatePopis() );
    $("#CashForm").valid();
}

function countAllChecked() {
    var total = 0.0;
    $(".invoice2settle:checked").each(function (i) {
        total = total + parseFloat($(this).attr('data-value'));
    });
    return total;
}

function updatePopis() {
    var popis = "Úhrada faktury: ";
    $(".invoice2settle:checked").each(function (i) {
        popis = popis + $(this).attr('data-kod') + "; ";
    });
    return popis;
}