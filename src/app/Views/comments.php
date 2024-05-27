<?= $this->extend('Layouts/default') ?>
<?= $this->section('content') ?>
<?php helper('form'); ?>


<style>
    .sort-indicator {
        display: inline-block;
        width: 0;
        height: 0;
        margin-left: 5px;
        /* vertical-align: middle; */
        border-left: 5px solid transparent;
        border-right: 5px solid transparent;
        border-bottom: 5px solid white;
    }

    .asc .sort-indicator {
        border-top: 5px solid transparent;
        border-bottom: 5px solid white;
    }

    .desc .sort-indicator {
        border-bottom: 5px solid transparent;
        border-top: 5px solid white;
    }

    #form-messages {
        display: none;
    }
</style>

<section class="comments">
    <h1>Comments Form</h1>


    <div class="btn-group mb-4" role="group">
        <button id="sortById" class="btn btn-primary">Сортировать по ID <span class="sort-indicator"></span></button>
        <button id="sortByDate" class="btn btn-primary">Сортировать по дате <span class="sort-indicator"></span></button>
    </div>

    <div id="comments-section">
        <!-- Комментарии будут загружены через AJAX -->
    </div>

    <?= form_open('commentForm', ['id' => 'commentForm', 'class' => 'mb-4']); ?>

    <div class="form-group">
        <label for="name">Ваш email</label>
        <input type="email" class="form-control" id="name" name="name" placeholder="Например test@gmail.com" value="test@gmail.com" required>
    </div>
    <div class="form-group">
        <label for="comment">Комментарий</label>
        <textarea class="form-control" id="comment" name="comment" rows="3" placeholder="Напишите ваш комментарий" required>ffff</textarea>
    </div>
    <button type="submit" class="btn btn-primary">Отправить</button>
    <?= form_close() ?>

    <div id="form-messages" class="alert alert-danger"></div>

    <?= $this->section('content-js') ?>
    <script>
        $(document).ready(function() {
            var sortOrder = {
                id: 'asc',
                date: 'asc'
            };

            var perPage = 3

            function getCurrentPage() {
                const url = window.location.pathname;
                const regex = /\/comments\/page\/(\d+)/;
                const matches = url.match(regex);

                if (matches) {
                    const page = parseInt(matches[1], 10);
                    return page;
                }
                return 1;
            }

            function ajaxErrorFunction(jqXHR, textStatus, errorThrown) {
                console.error('Error:', textStatus, errorThrown);

                if (jqXHR.status === 400) {
                    var response = JSON.parse(jqXHR.responseText);
                    alert('Error: ' + response.error);
                } else {
                    alert('Unexpected error occurred.');
                }
            }

            async function loadComments(page = 1, sortBy, sortOrder) {
                await $.ajax({
                    url: `/comments/page/${page}/sort_by/${sortBy}/sort_order/${sortOrder}`,
                    method: 'GET',
                    headers: {
                        "Content-Type": "application/json",
                        "X-Requested-With": "XMLHttpRequest"
                    },
                    success: function(response) {
                        console.log('success');
                        if (response.status === 'success') {
                            $('#comments-section').html('');
                            console.log('response', response);
                            response.comments.forEach(comment => {
                                $('#comments-section').append(`
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <p class="card-title">${comment.name}</p>
                                            <date class="card-subtitle mb-2 text-muted">${comment.date}</date>
                                            <p class="card-text">${comment.text}</p>
                                            <button class="btn btn-danger delete-comment" data-id="${comment.id}">Удалить</button>
                                        </div>
                                    </div>
                                `);
                            });
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        ajaxErrorFunction(jqXHR, textStatus, errorThrown)
                    }
                });
            }

            $('#sortById').on('click', async function() {
                sortOrder.id = sortOrder.id === 'asc' ? 'desc' : 'asc';
                await loadComments(getCurrentPage(), 'id', sortOrder.id);
                $(this).toggleClass('asc', sortOrder.id === 'asc');
                $(this).toggleClass('desc', sortOrder.id === 'desc');
            });

            $('#sortByDate').on('click', async function() {
                sortOrder.date = sortOrder.date === 'asc' ? 'desc' : 'asc';
                await loadComments(getCurrentPage(), 'date', sortOrder.date);
                $(this).toggleClass('asc', sortOrder.date === 'asc');
                $(this).toggleClass('desc', sortOrder.date === 'desc');
            });

            $('#commentForm').on('submit', function(e) {
                e.preventDefault();

                var name = $('#name').val()
                var text = $('#comment').val()

                var formData = new FormData()
                formData.append('name', name)
                formData.append('text', text)

                $.ajax({
                    url: `/comments/create`,
                    method: 'POST',
                    headers: {
                        "X-Requested-With": "XMLHttpRequest"
                    },
                    processData: false,
                    data: formData,
                    mimeType: "multipart/form-data",
                    contentType: false,
                    success: function(response) {
                        var resJson = JSON.parse(response)
                        console.log('response', typeof resJson, resJson);
                        if (resJson.status === 'success') {
                            console.log("$('#comments-section')", $('#comments-section'));

                            if ($('.card.mb-3').length < perPage) {
                                $('#comments-section').append(`
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <p class="card-title">${resJson.data.name}</p>
                                            <date class="card-subtitle mb-2 text-muted">${resJson.data.date}</date>
                                            <p class="card-text">${resJson.data.text}</p>
                                            <button class="btn btn-danger delete-comment" data-id="${resJson.data.id}">Удалить</button>
                                        </div>
                                    </div>
                                `);
                            }
                            $('#commentForm')[0].reset();
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('Error:', textStatus, errorThrown);

                        if (jqXHR.status === 400) {
                            var response = JSON.parse(jqXHR.responseText);

                            if (response.hasOwnProperty('validate_errors')) {
                                var errors = response['validate_errors']

                                var errorList = '<ul>';
                                $.each(errors, function(key, value) {
                                    errorList += '<li>' + value + '</li>';
                                });
                                errorList += '</ul>';
                                $('#form-messages').html(errorList).fadeIn();

                                setTimeout(function() {
                                    $('#form-messages').fadeOut();
                                }, 5000);

                                return
                            }
                        } else {
                            alert('Unexpected error occurred.');
                        }
                    }
                });
            });

            $('#comments-section').on('click', '.delete-comment', function() {
                var id = $(this).data('id');

                $.ajax({
                    url: `/comments/delete/${id}`,
                    method: 'DELETE',
                    headers: {
                        "Content-Type": "application/json",
                        "X-Requested-With": "XMLHttpRequest"
                    },
                    success: async function(response) {
                        if (response.status === 'success') {
                            $(`button[data-id='${id}']`).closest('.card').remove();
                        }

                        let currentPage = getCurrentPage()

                        await loadComments(currentPage, 'id', 'asc')
                        if ($('.card.mb-3').length < 1 && currentPage > 1) {
                            await loadComments(currentPage - 1, 'id', 'asc')
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        ajaxErrorFunction(jqXHR, textStatus, errorThrown)
                    }
                });
            });

            // Initial load
            loadComments(getCurrentPage(), 'id', 'asc');
        });
    </script>
    <?= $this->endSection() ?>
</section>

<?= $this->endSection() ?>