<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<?php
/**
 * Created by JetBrains PhpStorm.
 * User: phantrongvu
 * Date: 29/12/12
 * Time: 10:55 AM
 * To change this template use File | Settings | File Templates.
 */
?>
<?php
  $arg_3 = $this->uri->segment(3);
  $arg_4 = urldecode($this->uri->segment(4));
  if($arg_4 === 'null')
  {
    $arg_4 = '';
  }
?>
<!--
<form class="form-search form-search-people" action="/people/search/student">
-->

  <fieldset>
    <legend>Search</legend>
    <div class="form-inline">
      <span class="help-block">Enter either email, first name or last name.</span>
      <input type="text" class="input-xlarge search-query"
             placeholder="Mail / First name / Last name"
             value="<?php echo $arg_4; ?>" />
      <select class="input-small select-type">
        <option value="student" <?php echo $arg_3 === 'student' ? 'selected="selected"' : '' ?>>Student</option>
        <option value="staff" <?php echo $arg_3 === 'staff' ? 'selected="selected"' : '' ?>>Staff</option>
      </select>
      <span class="btn btn-primary btn-search">Search</span>
    </div>
  </fieldset>

<!--
</form>
-->

<p>&nbsp;</p>

<?php if(isset($items)): ?>
  <?php echo $pagination; ?>

  <table class="table table-striped table-hover table-bordered responsive">
    <thead>
      <tr>
        <th></th>
        <th>Mail</th>
        <th data-hide="xsmall,phone,small">First name</th>
        <th data-hide="xsmall,phone">Last name</th>
        <th data-hide="xsmall,phone,small,medium,large,tablet">DOB</th>
        <th data-hide="xsmall,phone,small,medium,large,tablet">Address</th>
        <th data-hide="xsmall,phone,small,medium,large,tablet">Phone</th>
        <th data-hide="xsmall,phone">Actions</th>
      </tr>
    </thead>

    <tbody>
    <?php foreach($items as $person): ?>
      <tr>
        <td>&nbsp;</td>
        <td><?php echo $person->mail; ?></td>
        <td><?php echo $person->first_name; ?></td>
        <td><?php echo $person->last_name; ?></td>
        <td><?php echo date_to_display($person->dob); ?></td>
        <td><address>
        <?php
        echo implode('<br />',
          array(
            $person->street . ($person->additional ? '<br />' . $person->additional : ''),
            $person->city,
            $person->postcode,
            $person->state,
          ));
        ?>
        </address></td>
        <td>
          <address>
            <abbr title="Phone">P:</abbr> <?php echo $person->phone; ?><br />
            <abbr title="Mobile">M:</abbr> <?php echo $person->mobile; ?>
          </address>
        </td>
        <td>
          <?php if ($this->uri->segment(3) === 'student'): ?>
            <?php echo anchor('people/student/' . $person->sid, 'Edit', array('class' => 'btn btn-small')) ?>
            <?php echo anchor('calendar/event?sid=' . $person->sid, 'Add event', array('class' => 'btn btn-small')) ?>
            <?php echo anchor('people/student/' . $person->sid, 'Delete',
            array(
              'class' => 'btn btn-small confirm-delete btn-danger',
              'data-type' => 'student',
              'data-id' => $person->sid,
            )) ?>
            <br />
            <?php echo anchor('note/search/' . $person->sid, 'Note history', array('class' => 'btn btn-small')) ?>
            <?php echo anchor('note/manage?sid=' . $person->sid . '&destination=people/search/student', 'Add Notes', array('class' => 'btn btn-small')) ?>
          <?php else: ?>
            <?php echo anchor('people/staff/' . $person->uid, 'Edit', array('class' => 'btn btn-small')) ?>
            <?php echo anchor('people/staff/' . $person->uid, 'Delete',
              array(
                'class' => 'btn btn-small confirm-delete btn-danger',
                'data-type' => 'staff',
                'data-id' => $person->uid,
              )) ?>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>

  <?php echo $pagination; ?>

  <div id="modal-delete-person" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="model-delete-label">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <h3 id="model-delete-label">Delete a person</h3>
    </div>
    <div class="modal-body">
      <p>You are about to delete one person, this procedure is irreversible.</p>
      <p>Do you want to proceed?</p>
    </div>
    <div class="modal-footer">
      <?php echo anchor('people/delete/student/1', 'Yes', array('class' => 'btn btn-danger')); ?>
      <button class="btn" data-dismiss="modal" aria-hidden="true">No</button>
    </div>
  </div>
<?php endif; ?>
