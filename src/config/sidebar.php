<?php

return [
    [
        "icon"     => "icon-home2 mr-2",
        "tooltip"  => "Switch Dashboard",
        "text"     => "Switch Dashboard",
        "route"      => "purchase.summary",
        "priority" => 1
    ],
    [
        "icon"     => "fas fa-arrows-alt",
        "tooltip"  => "Create Purchase Requisition",
        "text"     => "Create Purchase Requisition",
        "url"      => "/purchase-requisitions/create",
        "priority" => 2
    ],
    [
        "icon"     => "fas fa-arrows-alt",
        "tooltip"  => "Approved Purchase Requisition",
        "text"     => "Approved Purchase Requisition",
        "url"      => "/purchase-requisitions/index",
        "priority" => 3
    ],
    [
        "icon"     => "fas fa-arrows-alt",
        "tooltip"  => "Pending Purchase Requisition",
        "text"     => "Pending Purchase Requisition",
        "url"      => "/purchase-requisitions/pending-requisition",
        "priority" => 4
    ],
    [
        "icon"     => "fas fa-arrows-alt",
        "tooltip"  => "Reject Purchase Requisition",
        "text"     => "Reject Purchase Requisition",
        "url"      => "/purchase-requisitions/requisition-rejection-list",
        "priority" => 5
    ],
    
    [
        "icon"     => "fas fa-arrows-alt",
        "text"     => "PO/Budget Complete List",
        "tooltip"  => "PO/Budget Complete List",
        "url"      => "/purchase-requisitions/po-budget-complete-list",
        "priority" => 6 
    ],
    [
        "icon"     => "fas fa-arrows-alt",
        "text"     => "All Requisition List",
        "tooltip"  => "All Requisition List",
        "url"      => "/purchase-requisitions/all-requisition",
        "priority" => 7
    ],
    // [
    //     "icon"     => "fas fa-arrows-alt",
    //     "tooltip"  => "Gate Pass",
    //     "text"     => "Gate Pass",
    //     "url"      => "/gate-pass",
    //     "priority" => 8
    // ],
    
    
];
