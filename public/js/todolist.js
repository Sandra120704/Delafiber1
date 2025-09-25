/**
 * Todo List JavaScript - Funcionalidad de lista de tareas
 * Sistema Delafiber - Gestión de Fibra Óptica
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        initializeTodoList();
    });

    /**
     * Inicializar funcionalidad de lista de tareas
     */
    function initializeTodoList() {
        // Toggle completar tarea
        $(document).on('change', '.todo-item input[type="checkbox"]', function() {
            const todoItem = $(this).closest('.todo-item');
            const todoId = todoItem.data('id');
            const isCompleted = $(this).is(':checked');
            
            toggleTodoStatus(todoId, isCompleted, todoItem);
        });

        // Eliminar tarea
        $(document).on('click', '.todo-item .remove-todo', function() {
            const todoItem = $(this).closest('.todo-item');
            const todoId = todoItem.data('id');
            
            if (confirm('¿Estás seguro de que deseas eliminar esta tarea?')) {
                deleteTodo(todoId, todoItem);
            }
        });

        // Agregar nueva tarea
        $('.add-todo-form').on('submit', function(e) {
            e.preventDefault();
            const todoText = $(this).find('input[type="text"]').val().trim();
            
            if (todoText) {
                addNewTodo(todoText);
            }
        });

        // Enter key para agregar tarea rápida
        $('.quick-add-todo').on('keypress', function(e) {
            if (e.which === 13) {
                const todoText = $(this).val().trim();
                if (todoText) {
                    addNewTodo(todoText);
                    $(this).val('');
                }
            }
        });

        // Drag and drop para reordenar
        if ($.fn.sortable) {
            $('.todo-list').sortable({
                handle: '.drag-handle',
                update: function(event, ui) {
                    updateTodoOrder();
                }
            });
        }
    }

    /**
     * Cambiar estado de una tarea
     */
    function toggleTodoStatus(todoId, isCompleted, todoItem) {
        $.ajax({
            url: baseUrl + 'tarea/cambiar-estado',
            method: 'POST',
            data: {
                todo_id: todoId,
                completed: isCompleted,
                csrf_token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // Aplicar estilo visual
                    if (isCompleted) {
                        todoItem.addClass('completed');
                        todoItem.find('.todo-text').addClass('text-strikethrough');
                    } else {
                        todoItem.removeClass('completed');
                        todoItem.find('.todo-text').removeClass('text-strikethrough');
                    }
                    
                    // Actualizar contadores
                    updateTodoCounters();
                    
                    // Mostrar mensaje de éxito
                    showToast('Tarea actualizada correctamente', 'success');
                } else {
                    // Revertir checkbox si hay error
                    todoItem.find('input[type="checkbox"]').prop('checked', !isCompleted);
                    showToast('Error al actualizar la tarea', 'error');
                }
            },
            error: function() {
                // Revertir checkbox si hay error
                todoItem.find('input[type="checkbox"]').prop('checked', !isCompleted);
                showToast('Error de conexión', 'error');
            }
        });
    }

    /**
     * Eliminar una tarea
     */
    function deleteTodo(todoId, todoItem) {
        $.ajax({
            url: baseUrl + 'tarea/eliminar',
            method: 'POST',
            data: {
                todo_id: todoId,
                csrf_token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // Animar eliminación
                    todoItem.fadeOut(300, function() {
                        $(this).remove();
                        updateTodoCounters();
                    });
                    
                    showToast('Tarea eliminada correctamente', 'success');
                } else {
                    showToast('Error al eliminar la tarea', 'error');
                }
            },
            error: function() {
                showToast('Error de conexión', 'error');
            }
        });
    }

    /**
     * Agregar nueva tarea
     */
    function addNewTodo(todoText) {
        $.ajax({
            url: baseUrl + 'tarea/agregar',
            method: 'POST',
            data: {
                todo_text: todoText,
                csrf_token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // Crear elemento HTML para la nueva tarea
                    const newTodoHtml = createTodoItemHtml(response.todo);
                    
                    // Agregar a la lista
                    $('.todo-list').prepend(newTodoHtml);
                    
                    // Animar entrada
                    $('.todo-list .todo-item:first').hide().fadeIn(300);
                    
                    // Limpiar formulario
                    $('.add-todo-form input[type="text"]').val('');
                    
                    // Actualizar contadores
                    updateTodoCounters();
                    
                    showToast('Tarea agregada correctamente', 'success');
                } else {
                    showToast('Error al agregar la tarea', 'error');
                }
            },
            error: function() {
                showToast('Error de conexión', 'error');
            }
        });
    }

    /**
     * Crear HTML para un elemento de tarea
     */
    function createTodoItemHtml(todo) {
        return `
            <div class="todo-item" data-id="${todo.id}">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="todo-${todo.id}" ${todo.completed ? 'checked' : ''}>
                    <label class="form-check-label todo-text ${todo.completed ? 'text-strikethrough' : ''}" for="todo-${todo.id}">
                        ${todo.texto}
                    </label>
                </div>
                <div class="todo-actions">
                    <button class="btn btn-sm btn-outline-primary edit-todo" title="Editar">
                        <i class="ti-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger remove-todo" title="Eliminar">
                        <i class="ti-trash"></i>
                    </button>
                    <span class="drag-handle" title="Arrastrar">
                        <i class="ti-menu"></i>
                    </span>
                </div>
            </div>
        `;
    }

    /**
     * Actualizar orden de las tareas
     */
    function updateTodoOrder() {
        const todoOrder = [];
        $('.todo-list .todo-item').each(function(index) {
            todoOrder.push({
                id: $(this).data('id'),
                order: index
            });
        });

        $.ajax({
            url: baseUrl + 'tarea/reordenar',
            method: 'POST',
            data: {
                todo_order: JSON.stringify(todoOrder),
                csrf_token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showToast('Orden actualizado', 'success');
                }
            }
        });
    }

    /**
     * Actualizar contadores de tareas
     */
    function updateTodoCounters() {
        const totalTodos = $('.todo-item').length;
        const completedTodos = $('.todo-item.completed').length;
        const pendingTodos = totalTodos - completedTodos;

        $('.total-todos').text(totalTodos);
        $('.completed-todos').text(completedTodos);
        $('.pending-todos').text(pendingTodos);

        // Actualizar barra de progreso si existe
        if (totalTodos > 0) {
            const progressPercentage = (completedTodos / totalTodos) * 100;
            $('.todo-progress .progress-bar').css('width', progressPercentage + '%');
            $('.todo-progress .progress-bar').text(Math.round(progressPercentage) + '%');
        }
    }

    /**
     * Mostrar toast de notificación
     */
    function showToast(message, type = 'info') {
        const toastClass = type === 'success' ? 'bg-success' : type === 'error' ? 'bg-danger' : 'bg-info';
        const toastHtml = `
            <div class="toast ${toastClass} text-white" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
                <div class="toast-body">
                    ${message}
                    <button type="button" class="btn-close btn-close-white float-end" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;
        
        $('body').append(toastHtml);
        $('.toast:last').toast('show');
        
        // Auto-remover después de 3 segundos
        setTimeout(function() {
            $('.toast:last').fadeOut(300, function() {
                $(this).remove();
            });
        }, 3000);
    }

    // Variable global para baseUrl
    if (typeof baseUrl === 'undefined') {
        window.baseUrl = window.location.origin + '/';
    }

})(jQuery);