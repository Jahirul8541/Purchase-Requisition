<x-pondit-limitless-master>
  <x-pondit-pl-card cardTitle="REQUISITION MANAGEMENT DASHBOARD">

    <div class="cards row">
      <a href="{{ route('purchase.index') }}" class="card red">
        <div class="tip">
            PURCHASE REQUISITION
        </div>
      </a>
      <a href="{{ route('work_order.approved_list') }}" class="card blue ">
      <div class="tip">
          PURCHASE ORDER
        </div>
      </a>
      <a href="{{ route('budget.index') }}" class="card green">
        <div class="tip">
          PURCHASE BUDGET
        </div>
      </a>
      <a href="{{ route('challan.index') }}" class="card green-blue">
        <div class="tip">
          PURCHASE CHALLAN
        </div>
      </a>
      <a href="{{ route('store-delivery.index') }}" class="card purple">
        <div class="tip">
          STORE REQUISITION
        </div>
      </a>
      <a href="{{ route('bill.index') }}" class="card marune">
        <div class="tip">
          BILL ADJUSTMENT
        </div>
      </a>
      {{-- <a href="{{ route('gate.index') }}" class="card marune">
        <div class="tip">
          GATE PASS
        </div>
      </a> --}}
    </div>
    
    <div class="row">
      <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2" style="border-left: .25rem solid #286dc7">
            <div class="card-body">
              <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                  <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Requested Requisitions (Today)</div>
                  <div id="requestedCount" class="h5 mb-0 font-weight-bold text-gray-800">--</div>
                </div>
                <div class="col-auto">
                  <i class="fa fa-paper-plane fa-2x text-gray-300"></i>
                </div>
              </div>
            </div>
        </div>
      </div>
      <div class="col-xl-4 col-md-6 mb-4">
        <div class="card shadow h-100 py-2" style="border-left: .25rem solid #FF8C00">
            <div class="card-body">
              <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                  <div class="text-xs font-weight-bold text-uppercase mb-1" style="color: #FF8C00">Pending Requisitions (Today)</div>
                  <div id="pendingCount" class="h5 mb-0 font-weight-bold text-gray-800">--</div>
                </div>
                <div class="col-auto">
                  <i class="fas fa-spinner fa-2x text-gray-300"></i>
                </div>
              </div>
            </div>
        </div>
      </div>
      <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
              <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                  <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Confirmed Requisitions (Today)</div>
                  <div id="confirmedCount" class="h5 mb-0 font-weight-bold text-gray-800">--</div>
                </div>
                <div class="col-auto">
                  <i class="fas fa-check fa-2x text-gray-300"></i>
                </div>
              </div>
            </div>
        </div>
      </div>
      <div class="col-xl-4 col-md-6 mb-4">
        <div class="card shadow h-100 py-2" style="border-left: .25rem solid #F64C72">
            <div class="card-body">
              <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                  <div class="text-xs font-weight-bold text-uppercase mb-1" style="color: #F64C72">Issued (Pending Delivery) Requisitions (Today)</div>
                  <div id="issuedCount" class="h5 mb-0 font-weight-bold text-gray-800">--</div>
                </div>
                <div class="col-auto">
                  <i class="fas fa-cube fa-2x text-gray-300"></i>
                </div>
              </div>
            </div>
        </div>
      </div>
      <div class="col-xl-4 col-md-6 mb-4">
        <div class="card shadow h-100 py-2" style="border-left: .25rem solid #14A76C">
            <div class="card-body">
              <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                  <div class="text-xs font-weight-bold text-uppercase mb-1" style="color: #14A76C">Delivered Requisitions (Today)</div>
                  <div id="deliveredCount" class="h5 mb-0 font-weight-bold text-gray-800">--</div>
                </div>
                <div class="col-auto">
                  <i class="fa fa-truck fa-2x text-gray-300"></i>
                </div>
              </div>
            </div>
        </div>
      </div>
      <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
              <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                  <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Rejected Requisitions (Today)</div>
                  <div id="rejectedCount" class="h5 mb-0 font-weight-bold text-gray-800">--</div>
                </div>
                <div class="col-auto">
                  <i class="fas fa-ban fa-2x text-gray-300"></i>
                </div>
              </div>
            </div>
        </div>
      </div>
    </div>
  </x-pondit-pl-card>
  @push('css')
   <style>
      .cards {
          display: flex;
          flex-wrap: wrap;
          justify-content: space-between;
          margin: 5px;
          gap: 15px;
      }

      .cards .red {
        background-color: #2196F3;
      }

      .cards .blue {
        background-color: #FF8C00;
      }

      .cards .green {
        background-color: #22c55e;
      }

      .cards .green-blue {
        background-color: #05807b;
      }
      .cards .purple {
        background-color: #870c46;
      }
      .cards .marune {
        background-color: #e04646;
      }

      .cards .card{
        flex-basis: calc(33.33% - 30px); 
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        height: 100px;
        color:white;
        border-radius: 10px;
        cursor: pointer;
        transition: 400ms transform, 400ms box-shadow, 400ms background-color;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
      }

      .cards .card a.tip {
        font-size: 1.2em;
        font-weight: 700;
        color: inherit; /* Inherit text color from the parent card */
        text-decoration: none; 
      }


      .cards .card .tip {
        font-size: 1.2em;
        font-weight: 700;
      }

      .cards .card p.second-text {
        font-size: 0.7em;
      }

      .cards .red:hover{
        transform: scale(1.1);
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.4);
        background-color: rgba(255, 255, 255, 0.1);
        color: #2196F3; 
        border: 2px solid #2196F3;
      }

      .cards .blue:hover {
        transform: scale(1.1);
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.4);
        background-color: rgba(255, 255, 255, 0.1);
        color: #FF8C00; 
        border: 2px solid #FF8C00;
      }

      .cards .green:hover {
        transform: scale(1.1);
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.4);
        background-color: rgba(255, 255, 255, 0.1);
        color: #22c55e; 
        border: 2px solid #22c55e;
      }
      .cards .green-blue:hover {
        transform: scale(1.1);
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.4);
        background-color: rgba(255, 255, 255, 0.1);
        color: #05807b; 
        border: 2px solid #05807b;
      }
      .cards .purple:hover {
        transform: scale(1.1);
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.4);
        background-color: rgba(255, 255, 255, 0.1);
        color: #870c46; 
        border: 2px solid #870c46;
      }
      .cards .marune:hover {
        transform: scale(1.1);
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.4);
        background-color: rgba(255, 255, 255, 0.1);
        color: #e04646; 
        border: 2px solid #e04646;
      }
      @media (max-width: 768px) {
          .cards .card {
              flex: 1 0 calc(100% - 30px); /* Change to single column on smaller screens */
          }
      }
      @media  (min-width: 768px) and (max-width: 1199px)  {
        .cards .card {
            flex: 1 0 calc(50% - 30px); /* Change to single column on medium-sized screens */
        }
    }
   </style>
  @endpush
  @push('js')
    <script>
      (function ($) {
          $(document).ready(function(){
          $("#collapseButton").click();
              updateSummary();
          })
      })(jQuery);

      function updateSummary() {
      $.ajax({
        url: '/purchase-requisitions/summary-data',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
          // console.log(response);
          $('#requestedCount').text(response);
          setTimeout(updateSummary, 6000);
        },
        error: function(xhr, status, error) {
          console.error('Error:', error);
        }
      });
    }

    </script>
  @endpush
</x-pondit-limitless-master>