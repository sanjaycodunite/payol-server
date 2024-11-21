{system_message}
{system_info}
<div class="card shadow ">
    <div class="card-header py-3">
        <div class="row">
            <div class="col-sm-8">
                <h4><b>Activate ICICI AEPS</b></h4>
            </div>
            <form id="aeps3_form" enctype="multipart/form-data">
                <div class="card-body">
                    <input type="hidden" value="<?php echo $site_url; ?>" id="siteUrl">
                    <input type="hidden" value="<?php echo $memberID; ?>" name="memberID">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <h5>Personal Detail</h5>
                            <hr />
                        </div>
                        <div class="col-md-4 col-sm-12">
                            <div class="form-group">
                                <label><b>First Name*</b></label>
                                <input type="text" class="form-control" id="first_name" name="first_name"
                                    placeholder="First Name" value="<?php echo set_value('first_name'); ?>">
                                <?php echo form_error('first_name', '<div class="error">', '</div>'); ?>
                                <div class="error" id="first_name_error"></div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-12">
                            <div class="form-group">
                                <label><b>Middle Name*</b></label>
                                <input type="text" class="form-control" id="last_name" name="middle_name"
                                    placeholder="Middle Name" value="<?php echo set_value('middle_name'); ?>">
                                <div class="error" id="middle_name_error"></div>

                            </div>
                        </div>

                        <div class="col-md-4 col-sm-12">
                            <div class="form-group">
                                <label><b>Last Name*</b></label>
                                <input type="text" class="form-control" id="last_name" name="last_name"
                                    placeholder="Last Name" value="<?php echo set_value('last_name'); ?>">
                                <?php echo form_error('last_name', '<div class="error">', '</div>'); ?>
                                <div class="error" id="last_name_error"></div>
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-12">
                            <div class="form-group">
                                <label><b>Father Name.*</b></label>
                                <input type="text" class="form-control" name="father_name" id="father_name"
                                    placeholder="Father Name." value="<?php echo set_value('father_name'); ?>">
                                <div class="error" id="father_name_error"></div>
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-12">
                            <div class="form-group">
                                <label><b>Mother Name.*</b></label>
                                <input type="text" class="form-control" name="mother_name" id="mother_name"
                                    placeholder="Mother Name." value="<?php echo set_value('mother_name'); ?>">
                                <div class="error" id="mother_name_error"></div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-12">
                            <div class="form-group">
                                <label><b>User Date Of Birth*</b></label>
                                <input type="date" class="form-control" name="person_dob" id="person_dob"
                                    placeholder="Person Date of birth" value="<?php echo set_value('person_dob'); ?>">
                                <div class="error" id="person_dob_error"></div>

                            </div>
                        </div>

                        <div class="col-md-4 col-sm-12">
                            <div class="form-group">
                                <label for="gender"><b>Gender*</b></label>
                                <select class="form-control" id="gender" name="gender">
                                    <option value="">Select Gender</option>
                                    <option value="Male" <?php echo set_select('gender', 'Male'); ?>>Male</option>
                                    <option value="Female" <?php echo set_select('gender', 'Female'); ?>>Female</option>
                                    <option value="Other" <?php echo set_select('gender', 'Other'); ?>>Other</option>
                                </select>
                                <div class="error" id="gender_error"></div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-12">
                            <div class="form-group">
                                <label><b>Mobile No.*</b></label>
                                <input type="text" class="form-control" id="mobile" name="mobile"
                                    placeholder="Mobile No." value="<?php echo set_value('mobile'); ?>">
                                <div class="error" id="mobile_error"></div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-12">
                            <div class="form-group">
                                <label><b>Email Address*</b></label>
                                <input type="text" class="form-control" id="email" name="email" placeholder="Email"
                                    value="<?php echo set_value('email'); ?>">
                                <div class="error" id="email_error"></div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-12">
                            <div class="form-group">
                                <label><b>Aadhar No.*</b></label>
                                <input type="text" class="form-control" name="aadhar_no" id="aadhar_no"
                                    placeholder="Aadhar No." value="<?php echo set_value('aadhar_no'); ?>">
                                <div class="error" id="aadhar_no_error"></div>
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-12">
                            <div class="form-group">
                                <label><b>Pancard No.*</b></label>
                                <input type="text" class="form-control" name="pancard_no" id="pancard_no"
                                    placeholder="Pancard No." value="<?php echo set_value('pancard_no'); ?>">
                                <div class="error" id="pancard_no_error"></div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <h5>Address</h5>
                            <hr />
                        </div>

                        <div class="col-md-4 col-sm-12">
                            <div class="form-group">
                                <label><b>Street/Locality*</b></label>
                                <input type="text" class="form-control" name="street_locality"
                                    placeholder="Street/Locality" value="<?php echo set_value('street_locality'); ?>" />
                                <div class="error" id="street_locality_error"></div>
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-12">
                            <div class="form-group">
                                <label><b>Village*</b></label>
                                <input type="text" class="form-control" name="village" id="village"
                                    placeholder="Village" value="<?php echo set_value('village'); ?>">
                                <div class="error" id="village_error"></div>
                            </div>
                        </div>


                        <div class="col-md-4 col-sm-12">
                            <div class="form-group">
                                <label><b>Post Office*</b></label>
                                <input type="text" class="form-control" name="post_office" id="post_office"
                                    placeholder="Post Office" value="<?php echo set_value('post_office'); ?>">
                                <div class="error" id="post_error"></div>
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-12">
                            <div class="form-group">
                                <label><b>Police Station*</b></label>
                                <input type="text" class="form-control" name="police_station" id="police_station"
                                    placeholder="Police Station" value="<?php echo set_value('police_station'); ?>">
                                <div class="error" id="police_station_error"></div>
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-12">
                            <div class="form-group">
                                <label><b>Block*</b></label>
                                <input type="text" class="form-control" name="block" id="block" placeholder="Block"
                                    value="<?php echo set_value('block'); ?>">
                                <div class="error" id="block_error"></div>
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-12">
                            <div class="form-group">
                                <label><b>District*</b></label>
                                <input type="text" class="form-control" name="district" id="district"
                                    placeholder="District" value="<?php echo set_value('district'); ?>">
                                <div class="error" id="district_error"></div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-12">
                            <div class="form-group">
                                <label><b>State*</b></label>
                                <select class="form-control" name="state_id" id="selState">
                                    <option value="">Select State</option>
                                    <?php if ($stateList) { ?>
                                    <?php foreach ($stateList as $list) { ?>
                                    <option value="<?php echo $list['id']; ?>"><?php echo $list['state']; ?></option>
                                    <?php } ?>
                                    <?php } ?>
                                </select>
                                <div class="error" id="state_error"></div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-12">
                            <div class="form-group">
                                <label><b>City*</b></label>
                                <select class="form-control" name="city_id" id="selCity">
                                    <option value="">Select City</option>

                                </select>
                                <div class="error" id="city_id_error"></div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-12">
                            <div class="form-group">
                                <label><b>PIN Code*</b></label>
                                <input type="text" class="form-control" name="pin_code" id="pin_code"
                                    placeholder="PIN Code" value="<?php echo set_value('pin_code'); ?>">
                                <div class="error" id="pin_code_error"></div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <h5>Bank Details</h5>
                            <hr />
                        </div>


                        <div class="col-md-3 col-sm-12">
                            <div class="form-group">
                                <label><b>Bank Account Number*</b></label>
                                <input type="text" class="form-control" id="account_no" name="account_no"
                                    placeholder="Account no" value="<?php echo set_value('account_no'); ?>">
                                <div class="error" id="account_no_error"></div>
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-12">
                            <div class="form-group">
                                <label><b>Bank IFSC*</b></label>
                                <input type="text" class="form-control" id="bank_ifsc" name="bank_ifsc"
                                    placeholder="Bank Ifsc" value="<?php echo set_value('bank_ifsc'); ?>">
                                <div class="error" id="bank_ifsc_error"></div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-12">
                            <div class="form-group">
                                <label><b>Bank Name*</b></label>
                                <input type="text" class="form-control" id="bank_name" name="bank_name"
                                    placeholder="Bank Name" value="<?php echo set_value('bank_name'); ?>">
                                <div class="error" id="bank_name_error"></div>
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-12">
                            <div class="form-group">
                                <label><b>Branch Name*</b></label>
                                <input type="text" class="form-control" id="bank_branch_name" name="bank_branch_name"
                                    placeholder="Bank Branch Name" value="<?php echo set_value('bank_branch_name'); ?>">
                                <div class="error" id="bank_branch_name_error"></div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <h5>Shop/Business Details</h5>
                            <hr />
                        </div>

                        <div class="col-md-4 col-sm-12">
                            <div class="form-group">
                                <label><b>Shop/Business Name*</b></label>
                                <input type="text" class="form-control" id="shop_business_name"
                                    name="shop_business_name" placeholder="Shop/Business Name"
                                    value="<?php echo set_value('shop_business_name'); ?>">
                                <div class="error" id="shop_business_name_error"></div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-12">
                            <div class="form-group">
                                <label><b>Shop/Business Address*</b></label>
                                <input type="text" class="form-control" id="shop_business_address"
                                    name="shop_business_address" placeholder="Shop/Business Address"
                                    value="<?php echo set_value('shop_business_address'); ?>">
                                <div class="error" id="shop_business_address_error"></div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-12">
                            <div class="form-group">
                                <label for="business_type"><b>Business Type*</b></label>
                                <select class="form-control" id="business_type" name="business_type">
                                    <option value="">Select Business Type</option>
                                    <option value="Retailer" <?php echo set_select('business_type', 'Retailer'); ?>>
                                        Retailer</option>
                                    <option value="Distributor"
                                        <?php echo set_select('business_type', 'Distributor'); ?>>Distributor</option>
                                    <option value="Other" <?php echo set_select('business_type', 'Other'); ?>>Other
                                    </option>
                                </select>
                                <div class="error" id="business_type_error"></div>
                            </div>
                        </div>


                        <div class="col-sm-12">
                            <h5>Document Upload</h5>
                            <hr />
                        </div>

                        <div class="col-md-4 col-sm-12">
                            <div class="form-group">
                                <label><b>Aadhar Front Photo*</b></label>
                                <input type="file" name="aadhar_front_photo">
                                <p>Note: Only jpg,png allowed, max size 2MB.</p>
                                <div class="error" id="aadhar_front_photo"></div>
                            </div>
                        </div>


                        <div class="col-md-4 col-sm-12">
                            <div class="form-group">
                                <label><b>Aadhar Back Photo*</b></label>
                                <input type="file" name="aadhar_back_photo">
                                <p>Note: Only jpg,png allowed, max size 2MB.</p>
                                <div class="error" id="aadhar_back_photo_error"></div>

                            </div>
                        </div>


                        <div class="col-md-4 col-sm-12">
                            <div class="form-group">
                                <label><b>Pancard Photo*</b></label>
                                <input type="file" name="pancard_photo">
                                <p>Note: Only jpg,png allowed, max size 2MB.</p>
                                <div class="error" id="pancard_photo_error"></div>

                            </div>
                        </div>

                        <div class="col-md-4 col-sm-12">
                            <div class="form-group">
                                <label><b>Bank Passbook/Statement Photo (Optional)</b></label>
                                <input type="file" name="bank_passbook_statement_photo">
                                <p>Note: Only jpg,png allowed, max size 2MB.</p>
                                <div class="error" id="bank_passbook_statement_photo_error"></div>

                            </div>
                        </div>
                        <div class="col-md-4 col-sm-12">
                            <div class="form-group">
                                <label><b>User Photograph</b></label>
                                <input type="file" name="user_photo">
                                <p>Note: Only jpg,png allowed, max size 2MB. Upload your recent passport-size
                                    photograph.</p>
                                <div class="error" id="user_photo_error"></div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-12">
                            <div class="form-group">
                                <label><b>ShopPhotograph (Optional)</b></label>
                                <input type="file" name="shop_photo">
                                <p>Note: Only jpg,png allowed, max size 2MB. Upload your recent passport-size
                                    photograph.</p>
                                <div class="error" id="shop_photo_error"></div>

                            </div>
                        </div>

                        <div class="card-header py-3 text-right">
                            <button type="submit" class="btn btn-success aeps3btn">Submit</button>
                            <button onclick="window.history.back()" type="button"
                                class="btn btn-secondary">Cancel</button>
                        </div>
                        <div class="alert alert-warning mt-3 col-md-12 col-sm-12" role="alert">
                            <h5 class="text-danger"><strong>Note:</strong></h5>
                            <p class="mb-1 "><span class="text-danger">*</span> All fields marked with an asterisk are
                                <b>mandatory.</b>
                            </p>
                            <p class="mb-1">Ensure uploaded documents are <span class="font-weight-bold">clear</span>
                                and
                                meet the size and format requirements.</p>
                            <p class="mb-0">Verify all details before submission to avoid errors.</p>
                        </div>
                    </div>
            </form>
        </div>
    </div>
</div>