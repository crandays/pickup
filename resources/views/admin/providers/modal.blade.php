<div class="modal" id="updateWallet"tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Wallet_Balance</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Wallet Balance: U<span id="wallet_balance"></span></p>
		<form class="form-horizontal" action="provider/AddToWallet" onsubmit="return checkForm(this);" method="POST">
            	{{csrf_field()}}
				<input type="hidden" id="id" name="id" value="">
		<div class="form-group">
                  <label for="recipient-name" class="control-label">Enter Balance:</label>
                  <input type="number" step="0.5" class="form-control" name="amount" placeholder="Enter Amount to add" id="user-job" required>
               </div>
      </div>
      <div class="modal-footer">
        <button type="submit" name="bttn_submit" class="btn btn-primary">Save changes</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
	  </form>
    </div>
  </div>
</div>
