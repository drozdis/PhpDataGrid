<?php
namespace Widget\Grid\Extension;
use Widget\AbstractDecorator;

/**
 * @author Drozd Igor <drozd.igor@gmail.com>
 */
class ColumnsDecorator extends AbstractDecorator
{
    /**
     * {@inheritdoc}
     */
    public function render($content)
    {
        //формирование контента
        $list1 = $list2 = '';
        foreach ($this->getElement()->getColumns() as $column) {
            if ($column->isHidden() == false) {
                $list1 .= '<li id="col-' . $column->getName() . '"><a>' . $column->getTitle() . '</a></li>';
            } else {
                $list2 .= '<li id="col-' . $column->getName() . '"><a>' . $column->getTitle() . '</a></li>';
            }
        }
        $sorter = '<div class="col1"><h5>Используются</h5><ul id="sortable1" class="droptrue nav nav-list bs-docs-sidenav">' . $list1 . '</ul></div>';
        $sorter .= '<div class="col2"><h5>Скрытые</h5><ul id="sortable2" class="dropfalse nav nav-list bs-docs-sidenav">' . $list2 . '</ul></div>';

        $dialog =
            '<script type="text/javascript">
                function applySorting()
                {
                    var columns = $("#sortable1").sortable("toArray");
                    var disabled = $("#sortable2").sortable("toArray");
                    ' . $this->getElement()->getJavascriptObject() . '.load(null, {"extension-columns" : {columns : columns, disabled : disabled}});
                }

                function clearSorting()
                {
                    dialog.confirm("Восстановить начальное состояние таблицы?", function (result) {
                          if (result) ' . $this->getElement()->getJavascriptObject() . '.load(null, {"extension-columns" : {clear : 1}});
                    });
                }
            </script>

            <div id="grid-extension-columns" class="modal" style="display: none; width:730px;" aria-hidden="true">
                <div class="modal-header">
                    <button data-dismiss="modal" class="close" type="button">×</button>
                    <h3 class="pull-left">Настройка таблицы</h3>
                    <div class="clr"></div>
                </div>
                <div class="modal-body" style="min-width:700px; width:700px; height:500px;">
                    <div class="alert alert-info">Для настройки таблицы: <br/>1. Перетащите нужные Вам столбцы в поле "Используется" (все столбцы, находящиеся в поле "Скрытые" в таблице отображатся не будут). <br/>2. Задать порядок полей можно перемещением названий столбцов в рамках поля "Используется"</div>
                    ' . $sorter . '
                </div>
                <div class="modal-footer">
                    <a rel="nofollow" data-dismiss="modal" class="btn btn-primary" href="#" onclick="applySorting();">Применить</a>
                    <a rel="nofollow" data-dismiss="modal" class="btn btn-primary" href="#" onclick="clearSorting();">Восстановить</a>
                    <a rel="nofollow" data-dismiss="modal" class="btn" href="#">Закрыть</a>
                </div>
            </div>';

        $js =
            '$(function () {
                $( "ul.droptrue" ).sortable({
                    connectWith: "ul"
                });

                $( "ul.dropfalse" ).sortable({
                    connectWith: "ul"
                });
                $( "#sortable1, #sortable2").disableSelection();
            });';
        $this->getElement()->getResourceManager()->addJavascript($js);

        return str_replace('<div class="grid_ct">', '<div class="grid_ct">' . $dialog, $content);
    }
}
