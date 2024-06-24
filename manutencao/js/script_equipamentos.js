

// $(document).ready(function() {
//     $(".sortable").on("click", function() {
//         var table = $(this).closest("table");
//         var tbody = table.find("tbody");
//         var rows = tbody.find("tr").get();
//         var index = $(this).index();

//         rows.sort(function(a, b) {
//             var aValue = $(a).find("td").eq(index).text();
//             var bValue = $(b).find("td").eq(index).text();

//             return $.isNumeric(aValue) && $.isNumeric(bValue) ? aValue - bValue : aValue.localeCompare(bValue);
//         });

//         tbody.empty();
//         $.each(rows, function(index, row) {
//             table.append(row);
//         });
//         var thText = $(this).text();
//         var newText = thText.includes(" (Ordenado)") ? thText.replace(" (Ordenado)", "") : thText + " (Ordenado)";
//         $(this).html(newText);
//     });
// });


$(document).ready(function() {
    $(".sortable").on("click", function() {
        var table = $(this).closest("table");
        var tbody = table.find("tbody");
        var rows = tbody.find("tr").get();
        var index = $(this).index();

        rows.sort(function(a, b) {
            var aValue = $(a).find("td").eq(index).text();
            var bValue = $(b).find("td").eq(index).text();

            return $.isNumeric(aValue) && $.isNumeric(bValue) ? aValue - bValue : aValue.localeCompare(bValue);
        });

        tbody.empty();
        $.each(rows, function(index, row) {
            table.append(row);
        });

        // Remover o texto " (Ordenado)" de todos os cabeçalhos de coluna
        $(".sortable").not(this).each(function() {
            var thText = $(this).text();
            $(this).text(thText.replace(" ▼", ""));
        });

        // Adicionar o texto " (Ordenado)" ao cabeçalho de coluna clicado
        var thText = $(this).text();
        var newText = thText.includes(" ▼") ? thText.replace(" ▼", "") : thText + " ▼";
        $(this).text(newText);
    });
});
