$(document).ready(function() {
  $("form.selector input").change(function(e) {
    var prefix = e.target.form.id.split("-").slice(0, -1).join("-");
    var table = $("table#" + prefix + "-table")[0];
    var column = $(table).find("th[data-name='" + e.target.value + "']")[0];
    var index = $(table).find("thead tr *").index(column) + 1;

    $(table).find("td:nth-child(" + index + "), th:nth-child(" + index + ")").toggle(500);
  });
});
