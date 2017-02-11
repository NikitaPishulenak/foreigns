<form id="addForm">
    <div class="dateGroup">
        <span class="field">Дата прибытия</span>
        <input type="text" name="datu" class="datepicker" value="<?php echo $dat; ?>">
    </div>
	
    <div class="dateGroup">
        <span class="field">Дата ходатайства</span>
        <input type="text" name="datuhod" class="datepicker" value="<?php echo $dathod; ?>">
    </div>
	
    <span id="describe" class="field"></span>
    <textarea name="fio"><?php echo $fio; ?></textarea>
</form>