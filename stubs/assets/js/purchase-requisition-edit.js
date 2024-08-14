(function ($) {
    $(document).ready(function () {
        unit_select();
        product_select();
        qty_calculate();
        $(document).on("change", ".product_title", doubleSelectCheck);
        $(document).on("click", ".add_item", add_new_item);
        $(document).on("click", ".remove_item", removed_items_row);
        $(document).on("click", ".update_data", update);
        $(document).on("click", ".approve_btn", get_purchases_item_data);
        $(document).on("keyup", ".qty_needs, .qty_approves, .unit_rate", qty_calculate);
        $(document).on("change", "#unit", section_select);
    });
})(jQuery);

function doubleSelectCheck (event){
    // let el = event.target,
    //     itemSelect  = $(document).find('.product_title');
    // item = [];
    // $.each(itemSelect, function(index, v){
    //     if($(v).val() != null){
    //         if(v != el){
    //             item.push($(v).val());
    //         }
    //     }
    // });
    // if(item.includes($(el).val())){
    //     pl_swal('This Item Is Already Selected')
    //     $(el).val(null).change();
    // };

    let row = $(this).closest('tr');
    let id = row.find(".product_title").val();
    $.ajax({
        url: "/purchase-requisitions/get-product-data/"+ id,
        method: "get",
        type: "json",
        success: function (res) {
            $("#purchase_table tbody").each(function(){
                row.find(".uomTitle").val(res.uom_title);
            })
        },
    });
    
}

function add_new_item() {

    if ($("tbody input").val() == null || !$("tbody input").val()) {
        alert("please insert!");
        return false;
    }

    let dynamic_id = new Date().getTime();

    let id = $(document).find("#purchase_table>tbody>tr").length + 1;

    let tr = `
        <tr id="general_store_${id}">
            <td>
                <select id="product_title_${dynamic_id}" class="form-control product_title " required></select>
            </td>
            <td>
                <input type="number"  id="qty_needs" name="qty_needs"
                    class="form-control qty_needs" required>
            </td>
            <td>
            <input type="text" name="" class="form-control text-center uomTitle" readonly>
            </td>
            <td>
                <input type="text" name="purpose" class="form-control purpose" id="purpose" placeholder="PURPOSE">
            </td>
            <td>
                <input type="text" name="description" class="form-control description" id="description" placeholder="DESCRIPTION">
            </td>

            <td>
                <input type="text" class="form-control remarks text-danger" name="remarks" id="remarks" placeholder="REMARKS"> 
            </td>
            <td class="text-center">
                <a class="remove_item" href="javascript:void(0);"><i
                        class="fa fa-times text-danger"></i></a>
            </td>
        </tr>
            `;
    $("#purchase_table tbody").append(tr);
    product_title(`#product_title_${dynamic_id}`);
}

function removed_items_row() {
    let row =  $(this).closest("tr")
   let item_id = row.find('.item_id').val()
   let itemId = item_id?item_id:null;

   if (itemId != null) {
    if (confirm("Are you sure?")) $(this).closest("tr").remove();
    $.ajax({
        url: `/purchase-requisitions/item-delete/${item_id}`,
        type: "delete",
        headers: {
            "X-CSRF-TOKEN": $('input[name="_token"]').val(),
        },
        success:function(res){
            swalInit.fire({
                title   : res.msg,
                type    : 'success'
            })
        }
        })
    } else {
            if (confirm("Are you sure?")) $(this).closest("tr").remove();
    }
 
 }


function qty_calculate() { 
    let el = $(this).closest('tr'),
        qty = el.find('.qty_needs').val(),
        approveQty = el.find('.qty_approves').val() || 0,
        unit_rate = el.find('.unit_rate').val();
        if (approveQty != 0) {
            let total_amount =  approveQty * unit_rate ;
          let  roundedtotal = Math.ceil(total_amount * 100) / 100;
            el.find('.total_amount').val(roundedtotal.toFixed(2));
        }else{
            let total_amount =  qty * unit_rate ;
            let  roundedtotal = Math.ceil(total_amount * 100) / 100;
            el.find('.total_amount').val(roundedtotal.toFixed(2));
        }
    
}

function product_title(selector = null) {
    let product_id =  $('.product_title').attr('value');
    selector = selector != null ? selector : '.product_title';
    $.get('/purchase-requisitions/get-product-title', function (product_title) {
        $(selector).select2({
            placeholder: 'SELECT',
            data: product_title,
        }).val(product_id).trigger('change');
    });
}

async function product_select(selector = null) {
    const resp = await fetch("/purchase-requisitions/get-product-title");
    const data = await resp.json();
    for (const title of $(".product_title")) {
      const title_id = $(title).attr("value");
      
      $(title)
        .select2({
          placeholder: "SELECT",
          data,
        })
        .val(title_id)
        .trigger("change");
    }
}

function unit_select(selector = null) {
    selector = selector != null ? selector : '.unit';
    $.get('/purchase-requisitions/get-unit', function (section) {
        $(selector).select2({
            placeholder: 'SELECT SECTION',
            data: section,
        }).val($('#unit').attr('value')).trigger('change');
    });
}

function section_select(){
    let section_id =  $('#unit').val();
    $.get('/purchase-requisitions/get-section/' + section_id, function (unit) {
        $('.section').html('');
        $('.section').select2({
            placeholder: 'SELECT UNIT',
            data: unit,
        }).val($('#section').attr('value')).trigger('change');
    });
}


function update() {
    let json_data = {}; 
    json_data.id                   = $("#purchase_edi_id").val();
    json_data.unit_id              = $("#unit").val();
    json_data.section_id           = $("#section").val();
    json_data.contact_person       = $("#contact-person").val();
    json_data.contact_person_phone = $("#contact-person-phone").val();
    json_data.order_by             = $("#order_by").val();
    json_data.requisition_by_name  = $("#requisition-by-name").val();
    json_data.designation          = $("#designation").val();

    let tr_data = [];
    $("#purchase_requisition_tbody tr").each(function(){
        let item_data = {};
        item_data.item_id       = $(this).find('.item_id').val();
        item_data.product_title = $(this).find('.product_title').val();
        item_data.description   = $(this).find('.description').val();
        item_data.qty_needs     = $(this).find('.qty_needs').val();
        item_data.qty_approves  = $(this).find('.qty_approves').val() ?? null;
        item_data.unit_rate     = $(this).find('.unit_rate').val() ?? 0;
        item_data.purpose       = $(this).find('.purpose').val();
        item_data.total_amount  = $(this).find('.total_amount').val() ?? 0;
        item_data.remarks       = $(this).find('.remarks').val();

        tr_data.push(item_data);
    });

    json_data.items = tr_data;

    $.ajax({
        url    : "/purchase-requisitions/update",
        method : "post",
        data   : json_data,
        type   : "json",
        headers: {
            "X-CSRF-TOKEN": $('input[name="_token"]').val(),
        },
        success: function (res) {
            
            swalInit.fire({
                title   : res.msg,
                type    : 'success'
            }).then(function(){
                window.location = res.path;
            }); 
            
        },
        error: function (err) {
            console.log(err);
            swalInit.fire({
                title   : err.responseJSON.msg ?? "Something Went Wrong",
                type    : 'error'
            });
        },
    });
}


function get_purchases_item_data(e) {
    e.preventDefault();
    let id = $(this).closest('tr').find('.approve_btn').data('id');
    $.ajax({
        type: "get",
        url: "/purchase-requisitions/purchases-item-data/" + id,
        dataType: "json",
        success: function (response) {
            $('#modal-tbody').html("");
            $.each(response.purchases_item, function(key, item) { 
                
                let tr = `
                        <tr>
                            <td>
                            <input type="hidden" class="form-control purchases_item_id" data-id="${item.id}" id="purchases_item_id">
                                ${ ++key}
                            </td>
                            <td>
                                ${item.product_title}
                            </td>
                            <td>
                                ${item.qty}
                            </td>
                            <td>
                                ${item.qty_approves}
                            </td>
                            <td>
                                <input type="text" class="form-control approve_qty" id="approve_qty" name="approve_qty" placeholder="TYPE Approve Qty" />
                            </td>
                            
                        </tr>`
                $('#modal-tbody').append(tr);
            });                
        }
    });
    
}