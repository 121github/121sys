  <div class="panel panel-primary">
      <div class="panel-heading">Sticky Note</div>
      <div class="panel-body">
        <p>
          <textarea rows="3" class="form-control sticky-notes" placeholder="You can enter important notes here so they get seen. Eg. Do not call the customer before 3pm as they work night shifts!"><?php echo $details['record']['sticky_note'] ?></textarea>
        </p>
        <span class="alert-success hidden">Notes saved</span>
        <button class="btn btn-default pull-right save-notes">Save Notes</button>
      </div>
    </div>