$(function() {
    $('#select-archive').on('click', function() {
        let year = getSelectedValue('archive-year');
        let month = getSelectedValue('archive-month');
        let day = getSelectedValue('archive-day');
        window.location.href = '/' + year + '-' + month + '-' + day;
    });
});

function getSelectedValue(id)
{
    let select = document.getElementById(id);
    return select.options[select.selectedIndex].text;
}
