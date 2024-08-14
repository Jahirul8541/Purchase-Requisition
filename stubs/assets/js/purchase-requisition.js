(function ($) {
    $(document).ready(function () {
        unit_select();
        product_select();
        totalRate();

        $(document).on("change", ".product_title", doubleSelectCheck);
        $("#collapseButton").click();
        $(document).on('dblclick','.purchase_comments_edit', purchase_comment_edit);
        $(document).on('keypress','.purchase_comments_update', purchase_comments_update);

        // $(document).on("click", ".qty_select", check_full_qty);
        $(document).on("click", ".reject_comment_save", requisition_reject);
        $(document).on("click", ".add_item", add_new_item);
        $(document).on("click", ".remove_item", removed_items_row);
        $(document).on("click", ".save_data", store);
        $(document).on("click", ".approve_btn", get_purchases_item_data);
        $(document).on('click', '#modal-save', approved_qty);
        
        $(document).on("keyup", ".qty_needs, .unit_rate", qty_calculate);
        $(document).on("change", "#unit", section_select);
        $(document).on("click", ".user", get_approved_data);
        $(document).on("click", "#forward_approved", get_forward_approved);
        $(document).on("keyup", ".approve_qty", approved_qty_check);
        $(document).on("click", ".purchase_comment_save", purchase_comment_save);
        
    });
})(jQuery);
function doubleSelectCheck (event){
        let
            el = event.target,
            itemSelect  = $(document).find('.product_title');
        item = [];
        $.each(itemSelect, function(index, v){
            if($(v).val() != null){
                if(v != el){
                    item.push($(v).val());
                }
            }
        });

        if(item.includes($(el).val())){
            pl_swal('This Item Is Already Selected')
            $(el).val(null).change();
        };

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

function totalRate(){
    let sum = 0;

    $("#PurchasePreviewTable tbody tr .totalRate").each(function(){
        let newValue = $(this).text().replace(/,/g, '');
        sum += +newValue;
    });
    make_string(sum, '#grandTotal');
    // $("#grandTotal").text()
}

    function purchase_comment_edit(event)
    {
        el = event.target;
        text = $(el).text();
        $(el).html(`<input type="text" title="Edit & Press Enter" class="purchase_comments_update" class="form-control" value="${text}">`)
    }

    const purchase_comments_update = event =>
    {
        if (event.key === "Enter") {
            el = event.target;
            id = $(el).closest('tr').data('id');
            let data = $('.purchase_comments_update').val();

            $.get(`/purchase-requisitions/purchase-comment/ ${id}/${data}` ,function(res){
                
                $(el).closest('tr').find('.purchase_comments_edit').text($(el).val());
                
                swalInit.fire({
                    title: res.msg,
                    type : 'success',
                });
            });
        }
    }

//  function check_full_qty(){
//      let ischeck = $("#qty_select").prop('checked')
//      if(ischeck) {
//         $("#modal-tbody tr ").each(function(){
//         let qty = $(this).find('.qty').text();
//         let pre_qty = $(this).find('.pre_qty').text();
//             let print_qty = parseInt(qty) - parseInt(pre_qty);
//         $(this).find('.approve_qty').val(print_qty);
//         });
//     } else {
//         $("#modal-tbody tr ").each(function(){
//             $(this).find('.approve_qty').val('');
//             });
//     }
//  }

function requisition_reject(){
    let id = $('.purchases_req_id').val();
    let data = $("#reject_comment").val();
    console.log(data);
    $.ajax({
        url: "/purchase-requisitions/requisition-rejection/"+ id,
        method: "post",
        data: {comment:data},
        type: "json",
        headers: {
            "X-CSRF-TOKEN": $('input[name="_token"]').val(),
        },
        success: function (res) {
        $("#reject_modal").modal('hide');
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
                <input type="text" name="purpose" class="form-control purpose" id="purpose">
            </td>
            <td>
                <input type="text"  name="description" class="form-control description" id="description" placeholder="description" >
           </td>
            <td>
                <input type="text" class="form-control remarks text-danger" name="remarks" id="remarks">
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
    if (confirm("Are you sure?")) $(this).closest("tr").remove();
}



function qty_calculate() { 
    let el = $(this).closest('tr'),
        qty = el.find('.qty_needs').val(),
        unit_rate = el.find('.unit_rate').val();
    let total_amount =  qty * unit_rate ;
    
    el.find('.total_amount').val((total_amount).toFixed(2));
}

function product_title(selector = null) {
    let product_id =  $('#product_title').attr('value');
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

function store() {
  if (confirm("Are you sure You Want To Data Store?")) {
    
    let json_data = {}; 
    json_data.floor_req_id         = $(".floor_req_id").val();
    json_data.unit_id              = $("#unit").val();
    json_data.section_id           = $("#section").val();
    json_data.contact_person       = $("#contact-person").val();
    json_data.contact_person_phone = $("#contact-person-phone").val();
    json_data.order_by             = $("#order_by").val();
    json_data.requisition_by_name  = $("#requisition-by-name").val();
    json_data.designation          = $("#designation").val();

    let tr_data = [];
    $("#purchase_requisition_tbody tr").each(function(){

        let product_title = $(this).find('.product_title').val();
        if(product_title == undefined)
        {
            product_title = $(this).find('.product_id').val();
        }
        
        let item_data = {};
        item_data.product_title = product_title;
        item_data.description     = $(this).find('.description').val();
        item_data.qty_needs     = $(this).find('.qty_needs').val();
        item_data.unit_rate     = $(this).find('.unit_rate').val() ?? 0 ;
        item_data.purpose       = $(this).find('.purpose').val();
        item_data.total_amount  = $(this).find('.total_amount').val() ?? 0 ;
        item_data.remarks       = $(this).find('.remarks').val();

        tr_data.push(item_data);
    });

    json_data.items = tr_data;

    $.ajax({
        url: "/purchase-requisitions/store",
        method: "post",
        data: json_data,
        type: "json",
        headers: {
            "X-CSRF-TOKEN": $('input[name="_token"]').val(),
        },
        beforeSend: function () {
            $(".save_data").html('<option> Waiting ...</option>');
            $(".save_data").prop('disabled', true);
            },
        success: function (res) {

            swalInit.fire({
                title   : res.msg+ '<br>' + res.req_no,
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
            })
	    $(".save_data").html('<option> SUBMIT</option>');
            $(".save_data").prop('disabled', false);
        },
    });
  }

}


function approved_qty_check() {

        var row = $(this).closest('tr');
        $("#modal-tbody tr").each(function(){
        let qty = row.find('.qty').text();
        let approve_qty = row.find('.approve_qty').val();
        let pre_qty = row.find('.pre_qty').text();
        let alert_qty = parseInt(pre_qty) + parseInt(approve_qty);
            if (parseInt(qty)<alert_qty) {
                alert('Approve Quantity More Then Stock Quantity!')
                row.find('.approve_qty').val('')
            }
        });

        // var inputValues = [];

        // $('#modal-tbody .approve_qty').each(function() {
        // var inputValue = $(this).val();
        // inputValues.push(inputValue);
        // });

        // var isValueNotEmpty = inputValues.some(function(value) {
        // return value.trim() !== '';
        // });
        // if (isValueNotEmpty) {
        //     $("#ApproveBtn").removeClass('d-none');
        //     $("#modal-save").addClass('d-none');
        // }else{
        //     $("#ApproveBtn").addClass('d-none');
        //     $("#modal-save").removeClass('d-none');
        // }
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
                                <span class="qty">${item.qty}</span> (${item.uom_title})
                            </td>
                            <td>
                               <span class="pre_qty">${item.qty_approves}</apan> 
                            </td>
                            <td>
                                <input type="text" required class="form-control approve_qty" id="approve_qty" name="approve_qty" placeholder="TYPE Approve Qty" />
                            </td>
                            <td>
                                <input type="text" class="form-control comment" id="comment" name="comment" value="${item.comment ?? ''}" placeholder="Comment" />
                            </td>
                            
                        </tr>`
                $('#modal-tbody').append(tr);
            });                
        }
    });
    
}

 function approved_qty(btnId) {  
    if (confirm("Do You Want to Approve Quantity?")){
        // $("#modal-tbody tr ").each(function(){
        //     let qty = $(this).find('.qty').text();
        //     let pre_qty = $(this).find('.pre_qty').text();
        //     let print_qty = parseInt(qty) - parseInt(pre_qty);
        //     $(this).find('.approve_qty').val(print_qty);
        // });
        let allVal = [];   
        $("#modal-tbody tr").each(function() { 
            let itemData = {};
            itemData.item_id = $(this).find('.purchases_item_id').data('id');  
            itemData.approve_qty = $(this).find('.approve_qty').val();  
            itemData.comment = $(this).find('.comment').val();  
            allVal.push(itemData);
        });    
        let data = {
            item : allVal,
        };
        $.ajax({
            url    : "/purchase-requisitions/purchases-approve-qty",
            method : "post",
            data   : data,
            type   : "json",
            headers: {
                "X-CSRF-TOKEN": $('input[name="_token"]').val(),
            },
            success: function (res) {
            $("#item_modal").modal('hide');
                swalInit.fire({
                    title   : res,
                    type    : 'success'
                }).then(function(){
                    window.location.reload();
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
    };
}

function get_approved_data() {
    if(confirm("Are You Sure to Approve This?")){
        let el   = $(this);
        let data = {
            value: $(this).data("value"),
            id   : $(this).data("id"),
        }
        $.ajax({
            type: "post",
            url: "/purchase-requisitions/user-wise-approved/",
            data: data,
            headers: {
                "X-CSRF-TOKEN": $('input[name="_token"]').val(),
            },
            success: function (res) {
                window.location.reload();
                el.closest('td').html(res.data);
                swalInit.fire({
                    title   : res.msg,
                    type    : 'success'
                });
            }
        });
    }
}

function get_forward_approved() {
    let el = $(this);
    let data = {
        value : $(this).data("value"),
        id : $(this).data("id"),
    }
    deleteConfirmation(data);

    function deleteConfirmation(data) {
        swal.fire({
            title: "Are You Sure to Approve This?",
            icon: 'question',
            text: "Please ensure and then confirm!",
            type: "warning",
            showCancelButton: !0,
            confirmButtonText: "Confirm",
            cancelButtonText: "cancel",
            reverseButtons: !0
        }).then(function (e) {

            if (e.value === true) {

                $.ajax({
                    type: "post",
                    url: "/purchase-requisitions/user-forward-approved/",
                    data: data,
                    headers: {
                        "X-CSRF-TOKEN": $('input[name="_token"]').val(),
                    },
                    success: function (res) {
                        swalInit.fire({
                            title   : res.msg,
                            type    : 'success'
                        }).then(function(){
                            window.location.href = "/purchase-requisitions/";
                        }); 
                        
                    }
                });

            } else {
                e.dismiss;
            }
        }, function (dismiss) {
            return false;
        })
    }
    
}

function purchase_comment_save(){
    let id = $('.purchases_req_id').val();
    let comment = $('.purchase_comments').val();

    $.ajax({
        type: "get",
        url :`/purchase-requisitions/purchase-comment/${id}/${comment}`,
        headers: {
                "X-CSRF-TOKEN": $('input[name="_token"]').val(),
        },
        success : function(res){
            $('.purchase_comments').val('');
            swalInit.fire({
                title   : res.msg,
                type    : 'success'
            }).then(function(){
                window.location.href = "/purchase-requisitions/";
            }); 
        }
    })
}
