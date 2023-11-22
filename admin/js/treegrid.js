var id;
    var columns = [
        {
            title: 'Id',
            target: 0,
            data: function(data) {
                return data.id;
            }
        },
        {
            title: '',
            target: 1,
            width: "5%",
            className: 'treegrid-control',
            data: function (item) {
                if (item.children) {
                    id = item.id;
                    // style="width:30px; float:right;"
                    return '<span class="expand">+<\/span>';
                }
                return '';
            }
        },
        {
            title: 'Name',
            target: 2,
            data: function (item) {
                return item.name;
            }
        },
        {
            title: 'Description',
            target: 3,
            data: function (item) {
                return item.desc;
            }
        },
        {
            title: 'Status',
            target: 4,
            data: function(item) {
                return (item.is_active == 1) ? "Active" : "In-Active";
            }
        },
        {
            title: 'Action',
            target: 5,
            data: function (item) {
                return `<a href='update_categories.php?id=${item.id}' name='update' class='btn btn-sm btn-primary m-1'><i class="fas fa-edit" title='Update Category'></i></a>&nbsp;<button data-value='${item.id}' name='delete' class='btn btn-sm btn-danger'><i class="fa fa-trash" title='Delete Category' aria-hidden="true"></i></button>&nbsp;<a href='addsubcategory.php?id=${item.id}' name='subcategory' class='btn btn-sm btn-info'><i class="fa-solid fa-code-branch" title='Add Subcategory'></i></a></div>`;
            }
        }
    ];
    $(document).ready(function () {
        $('#categoryList').DataTable({
            'columns': columns,
            'ajax': {url: './arrays.php'},
            'treeGrid': {
                'left': 10,
                'expandIcon': '<span class="expand">+<\/span>',
                'collapseIcon': '<span class="expand">-<\/span>'
            },
            'columnDefs': [{
                "targets": 4,
                "orderable": false
            }]
        });

        $('h4').on('click', function () {
            var h4 = $(this);
            if (h4.hasClass('show')) {
                h4.removeClass('show').addClass('showed').html('-hide code');
                h4.next().removeClass('hide');
            }
            else {
                h4.removeClass('showed').addClass('show').html('+show code');
                h4.next().addClass('hide');
            }
        });
    });