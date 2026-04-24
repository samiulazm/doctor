$(document).ready(function () {
  "use strict";

  function escapeHtml(s) {
    if (s === null || s === undefined) {
      return "";
    }
    return $("<div/>").text(String(s)).html();
  }

  function escapeAttr(s) {
    return String(s || "")
      .replace(/&/g, "&amp;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#39;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;");
  }

  function badgeForAction(action) {
    var a = String(action || "");
    var cls = "badge badge-secondary";
    if (/\.(delete|remove|destroy)\b/i.test(a) || /^delete\b/i.test(a)) {
      cls = "badge badge-danger";
    } else if (a.indexOf("auth.") === 0) {
      cls = "badge badge-warning text-dark";
    } else if (a.indexOf("patient.") === 0) {
      cls = "badge badge-info";
    } else if (a.indexOf("finance.") === 0 || a.indexOf("payment") !== -1) {
      cls = "badge badge-success";
    } else if (a.indexOf("appointment.") === 0) {
      cls = "badge badge-primary";
    } else if (a.indexOf("settings.") === 0 || a.indexOf("config") !== -1) {
      cls = "badge badge-dark";
    } else if (/\.(create|add|insert)\b/i.test(a)) {
      cls = "badge badge-success";
    } else if (/\.(update|edit|save)\b/i.test(a)) {
      cls = "badge badge-info";
    }
    return '<span class="' + cls + '">' + escapeHtml(a) + "</span>";
  }

  function badgeForEntityType(t) {
    var raw = String(t || "");
    if (raw === "" || raw === "—") {
      return '<span class="text-muted">' + escapeHtml(raw || "—") + "</span>";
    }
    var cls = "badge badge-light border text-dark";
    var lower = raw.toLowerCase();
    if (lower.indexOf("patient") !== -1) {
      cls = "badge badge-info";
    } else if (lower.indexOf("user") !== -1 || lower.indexOf("staff") !== -1) {
      cls = "badge badge-primary";
    } else if (lower.indexOf("appointment") !== -1) {
      cls = "badge badge-success";
    } else if (lower.indexOf("invoice") !== -1 || lower.indexOf("payment") !== -1) {
      cls = "badge badge-warning text-dark";
    }
    return '<span class="' + cls + '">' + escapeHtml(raw) + "</span>";
  }

  var table = $("#audit-table").DataTable({
    responsive: true,
    processing: true,
    serverSide: true,
    stateSave: true,
    stateDuration: 60 * 60 * 24,
    ajax: {
      url: "logs/getAuditJson",
      type: "POST",
    },
    scroller: {
      loadingIndicator: true,
    },
    dom:
      "<'row audit-dt-toolbar mb-2'<'col-sm-12 col-md-4 col-lg-3'l><'col-sm-12 col-md-4 col-lg-5 text-center'B><'col-sm-12 col-md-4 col-lg-4 text-md-end'f>>" +
      "<'row'<'col-sm-12'tr>>" +
      "<'row'<'col-sm-5'i><'col-sm-7'p>>",
    buttons: [
      { extend: "copyHtml5", exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6] } },
      { extend: "excelHtml5", exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6] } },
      { extend: "csvHtml5", exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6] } },
      { extend: "pdfHtml5", exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6] } },
      { extend: "print", exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6] } },
    ],
    aLengthMenu: [
      [10, 25, 50, 100, -1],
      [10, 25, 50, 100, "All"],
    ],
    pageLength: 50,
    order: [[0, "desc"]],
    columnDefs: [
      { targets: 0, width: "11rem" },
      { targets: 1, orderable: true, render: function (data) {
          return badgeForAction(data);
        },
      },
      { targets: 2, render: function (data) {
          return badgeForEntityType(data);
        },
      },
      { targets: 5, render: function (data) {
          return '<span class="audit-ip-cell">' + escapeHtml(data) + "</span>";
        },
      },
      { targets: 6, orderable: false, render: function (data) {
          var full = data === "—" ? "" : String(data);
          return (
            '<span class="audit-details-cell" title="' +
            escapeAttr(full) +
            '">' +
            escapeHtml(data) +
            "</span>"
          );
        },
      },
    ],
    language: {
      lengthMenu: "_MENU_",
      search: "_INPUT_",
      url: "common/assets/DataTables/languages/" + language + ".json",
    },
  });

  table.on("xhr.dt", function (e, settings, json) {
    if (json && typeof json.recordsTotal === "number") {
      var el = document.getElementById("audit-total-count");
      if (el) {
        el.textContent = json.recordsTotal.toLocaleString();
      }
    }
  });

  table.buttons().container().appendTo(".custom_buttons");

  $("#audit-reload-table").on("click", function () {
    var $btn = $(this).addClass("is-spinning");
    table.ajax.reload(function () {
      $btn.removeClass("is-spinning");
    }, false);
  });
});
