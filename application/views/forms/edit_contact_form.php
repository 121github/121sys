
<ul class="nav nav-tabs" style=" background:#eee; width:100%;">
  <li class="active"><a href="#general" class="tab" data-toggle="tab">General</a></li>
  <li class="tab-alert">You must create the contact before adding phone numbers</li>
  <li class="phone-tab"><a href="#phone"  class="tab" data-toggle="tab">Phone Numbers</a></li>
  <li class="address-tab"><a href="#address"  class="tab" data-toggle="tab">Addresses</a></li>
</ul>

<!-- Tab panes -->
<div class="tab-content">
  <div class="tab-pane active" id="general">
    <form class="form-horizontal">
     <input name="urn" type="hidden" value="<?php echo $urn ?>">
      <input name="contact_id" type="hidden" value="">
      <?php if($_SESSION['config']['use_fullname']): ?>
            <div class="form-group input-group-sm">
        <input type="text" class="form-control" placeholder="Full name" name="fullname" value="">
      </div>
      <?php else: ?>
      <div class="form-group input-group-sm">
        <input type="text" class="form-control" placeholder="Title" name="title" value="">
      </div>
      <div class="form-group input-group-sm">
        <input type="text" class="form-control" placeholder="Firstname" name="firstname" value="">
      </div>
      <div class="form-group input-group-sm">
        <input type="text" class="form-control" placeholder="Lastname" name="lastname" value="">
      </div>
      <?php endif ?>
      <div class="form-group input-group-sm">
        <input type="text" class="form-control" placeholder="Job title" name="position" value="">
      </div>
       <div class='form-group input-group-sm' data-picktime="false">
            <input name="dob" placeholder="Date of birth" type='text' class="form-control dob" value=""/>
        </div>
      <div class="form-group input-group-sm">
        <input type="text" class="form-control" placeholder="Email Address" name="email" value="">
      </div>
      <div class="form-group input-group-sm">
        <input type="text" class="form-control" placeholder="Website address" name="website" value="">
      </div>
      <div class="form-group input-group-sm">
        <input type="text" class="form-control" placeholder="Linkedin profile page" name="linkedin" value="">
      </div>
      <div class="form-group input-group-sm">
        <input type="text" class="form-control" placeholder="Facebook profile page" name="facebook" value="">
      </div>
            <div class="form-group input-group-sm">
        <textarea class="form-control" name="notes" placeholder="Enter contact notes here" style="height:5em"></textarea>
      </div>
      <div class="form-actions pull-right">
      <span class="alert-success hidden">Contact details saved</span>
        <button type="submit" class="btn btn-primary save-contact-general">Save changes</button>
        <button class="btn btn-default close-contact-btn">Close</button>
      </div>
    </form>
  </div>
  <div class="tab-pane" id="phone">
    <div class="table-container">
      <p class="pull-right"><a href="#" class="contact-add-item">Add phone number</a><div class="clearfix"></div></p>
      <p class="none-found">There are no numbers linked to this contact</p>
      <table class="table">
        <thead>
        <th>Description</th>
          <th>Number</th>
          <th>TPS</th>
          <th>Options</th>
            </thead>
        <tbody>
        </tbody>
      </table>
    </div>
    <form class="form-horizontal">
      <input name="contact_id" type="hidden" value="">
      <input class="item-id" name="telephone_id" type="hidden" value="">
      <p>Enter the phone number details</p>
      <div class="form-group input-group-sm">
        <input type="text" class="form-control" placeholder="Description. Eg: Mobile" name="description" value="">
      </div>
      <div class="form-group input-group-sm">
        <input type="text" class="form-control" placeholder="Phone number" name="telephone_number" value="">
      </div>
            <div class="form-group input-group-sm">
     <select class="form-control selectpicker" placeholder="Is this number TPS registered?" name="tps">
     <option value="">Is this number TPS registered</option>
     <option value="1">Yes</option>
     <option value="0">No</option>
     <option value="">Don't know</option>
     </select>
      </div>
      <div class="form-actions pull-right">
        <button type="submit" class="btn btn-primary save-contact-phone" action="add_phone">Add number</button>
        <button class="btn btn-default close-contact-btn">Close</button>
      </div>
    </form>
  </div>
  <div class="tab-pane" id="address">
    <div class="table-container">
      <p class="pull-right"><a href="#" class="contact-add-item">Add another address</a><div class="clearfix"></div></p>
      <p class="none-found">There are no addresses linked to this contact</p>
      <table class="table">
        <thead>
        <th>Add1</th>
          <th>Postcode</th>
          <th>Primary</th>
          <th>Options</th>
            </thead>
        <tbody>
        </tbody>
      </table>
    </div>
    <form class="form-horizontal">
      <input class="item-id" name="address_id" type="hidden" value="">
      <input name="contact_id" type="hidden" value="">
      <p>Enter the address details</p>
      <div class="form-group input-group-sm">
        <input type="text" class="form-control" placeholder="1st Line of address" name="add1" value="">
      </div>
      <div class="form-group input-group-sm">
        <input type="text" class="form-control" placeholder="2nd Line of address" name="add2" value="">
      </div>
      <div class="form-group input-group-sm">
        <input type="text" class="form-control" placeholder="3rd Line of address" name="add3" value="">
      </div>
      <div class="form-group input-group-sm">
        <input type="text" class="form-control" placeholder="County" name="county" value="">
      </div>
      <div class="form-group input-group-sm">
        <input type="text" class="form-control" placeholder="Country" name="country" value="">
      </div>
      <div class="form-group input-group-sm">
        <input type="text" class="form-control" placeholder="Postcode" name="postcode" value="">
      </div>
            <div class="form-group input-group-sm">
           <select class="form-control selectpicker" placeholder="Is this the primary address?" name="primary">
     <option value="">Is this the primary address?</option>
     <option value="1">Yes</option>
     <option value="0">No</option>
     </select>
      </div>
      <div class="form-actions pull-right">
        <button type="submit" class="btn btn-primary save-contact-address" action="add_address">Add Address</button>
        <button class="btn btn-default close-contact-btn">Close</button>
      </div>
    </form>
  </div>
</div>
