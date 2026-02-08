<div class="panel">
    <div class="panel-heading"><i class="icon-search"></i> {l s='Détails' mod='formulaire'}</div>
    <div class="form-horizontal">
        <div class="row"><label class="control-label col-lg-3">Nom :</label><div class="col-lg-9"><p class="form-control-static">{$data.nom}</p></div></div>
        <div class="row"><label class="control-label col-lg-3">Email :</label><div class="col-lg-9"><p class="form-control-static">{$data.email}</p></div></div>
        <div class="row"><label class="control-label col-lg-3">Date :</label><div class="col-lg-9"><p class="form-control-static">{$data.date_add}</p></div></div>
        <div class="row"><label class="control-label col-lg-3">Message :</label><div class="col-lg-9"><div class="well">{$data.message|nl2br}</div></div></div>
    </div>
    <div class="panel-footer">
        <a href="{$link}" class="btn btn-default"><i class="process-icon-back"></i> Retour</a>
    </div>
</div>