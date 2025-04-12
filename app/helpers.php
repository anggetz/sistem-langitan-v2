<?php 
function pt() {
    if(app()->bound('pt')) {
        return app('pt');
    }
}
