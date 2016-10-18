<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<?php
/**
 * Created by JetBrains PhpStorm.
 * User: phantrongvu
 * Date: 29/12/12
 * Time: 12:22 PM
 * To change this template use File | Settings | File Templates.
 */
?>

<?php echo form_open('admin/save_permissions'); ?>
  <table class="table table-striped table-hover table-bordered responsive">
    <thead>
      <tr>
        <th>Roles/Permissions</th>
        <?php foreach($data['roles'] as $rid => $role): ?>
          <th><?php echo $role; ?></th>
        <?php endforeach; ?>
      </tr>
    </thead>
    <tbody>
      <?php foreach($data['permissions'] as $pid => $permission): ?>
        <tr>
          <th><?php echo $permission; ?></th>
          <?php foreach($data['roles'] as $rid => $role): ?>
            <td>
              <?php if($data['items'][$rid][$pid]): ?>
                <?php echo form_checkbox("role_permission[$rid][$pid]", 1, TRUE); ?>
              <?php else: ?>
                <?php echo form_checkbox("role_permission[$rid][$pid]", 1); ?>
              <?php endif; ?>
            </td>
          <?php endforeach; ?>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <div class="form-actions">
    <?php echo form_submit('Save', 'Save', 'class="btn btn-primary"'); ?>
  </div>
<?php echo form_close(); ?>
