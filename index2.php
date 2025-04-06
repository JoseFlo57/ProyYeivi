<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "empresaf";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to fetch employees from the database
function fetchEmployees($conn) {
    $sql = "SELECT * FROM empleados ORDER BY id DESC";
    $result = $conn->query($sql);

    $employees = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $employees[] = $row;
        }
    }
    return $employees;
}

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch($action) {
        case 'create':
            $nombres = $_POST['nombres'];
            $apellidos = $_POST['apellidos'];
            $fecha_nacimiento = $_POST['fecha_nacimiento'];
            $correo_electronico = $_POST['correo_electronico'];
            $direccion = $_POST['direccion'];
            $id_empleado = $_POST['id_empleado'];
            $departamento = $_POST['departamento'];
            $salario = $_POST['salario'];
            $genero = $_POST['genero'];
            $telefono = $_POST['telefono'];
            $ciudad = $_POST['ciudad'];
            $fecha_contratacion = $_POST['fecha_contratacion'];
            $cargo = $_POST['cargo'];
            $supervisor = $_POST['supervisor'];
            $notas_adicionales = $_POST['notas_adicionales'];

            $sql = "INSERT INTO empleados (nombres, apellidos, fecha_nacimiento, correo_electronico, 
                    direccion, id_empleado, departamento, salario, genero, telefono, ciudad, 
                    fecha_contratacion, cargo, supervisor, notas_adicionales) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssdsssssss", $nombres, $apellidos, $fecha_nacimiento, 
                            $correo_electronico, $direccion, $id_empleado, $departamento, 
                            $salario, $genero, $telefono, $ciudad, $fecha_contratacion, 
                            $cargo, $supervisor, $notas_adicionales);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Empleado agregado correctamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al agregar empleado: ' . $conn->error]);
            }
            break;

        case 'update':
            $id = $_POST['id'];
            $nombres = $_POST['nombres'];
            $apellidos = $_POST['apellidos'];
            $fecha_nacimiento = $_POST['fecha_nacimiento'];
            $correo_electronico = $_POST['correo_electronico'];
            $direccion = $_POST['direccion'];
            $id_empleado = $_POST['id_empleado'];
            $departamento = $_POST['departamento'];
            $salario = $_POST['salario'];
            $genero = $_POST['genero'];
            $telefono = $_POST['telefono'];
            $ciudad = $_POST['ciudad'];
            $fecha_contratacion = $_POST['fecha_contratacion'];
            $cargo = $_POST['cargo'];
            $supervisor = $_POST['supervisor'];
            $notas_adicionales = $_POST['notas_adicionales'];

            $sql = "UPDATE empleados SET nombres=?, apellidos=?, fecha_nacimiento=?, 
                    correo_electronico=?, direccion=?, id_empleado=?, departamento=?, 
                    salario=?, genero=?, telefono=?, ciudad=?, fecha_contratacion=?, 
                    cargo=?, supervisor=?, notas_adicionales=? WHERE id=?";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssdsssssssi", $nombres, $apellidos, $fecha_nacimiento, 
                            $correo_electronico, $direccion, $id_empleado, $departamento, 
                            $salario, $genero, $telefono, $ciudad, $fecha_contratacion, 
                            $cargo, $supervisor, $notas_adicionales, $id);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Empleado actualizado correctamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al actualizar empleado: ' . $conn->error]);
            }
            break;

        case 'delete':
            $id = $_POST['id'];
            
            $sql = "DELETE FROM empleados WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Empleado eliminado correctamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al eliminar empleado: ' . $conn->error]);
            }
            break;

        case 'get':
            $id = $_POST['id'];
            
            $sql = "SELECT * FROM empleados WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
                echo json_encode(['success' => true, 'data' => $row]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Empleado no encontrado']);
            }
            break;
    }
    exit;
}

// Fetch employees and encode as JSON
$employees = fetchEmployees($conn);
$employeesJson = json_encode($employees);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión de Empleados</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.12/dist/sweetalert2.all.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <style>
        .modal {
            transition: opacity 0.25s ease;
        }
        body.modal-active {
            overflow-x: hidden;
            overflow-y: visible !important;
        }
        .empleados-theme {
            --primary-color: #3b82f6;
            --primary-hover: #2563eb;
            --secondary-color: #60a5fa;
            --accent-color: #93c5fd;
        }
        .jefes-theme {
            --primary-color: #10b981;
            --primary-hover: #059669;
            --secondary-color: #34d399;
            --accent-color: #6ee7b7;
        }
        
        .loader {
            border-top-color: var(--primary-color);
            -webkit-animation: spinner 1.5s linear infinite;
            animation: spinner 1.5s linear infinite;
        }
        
        @-webkit-keyframes spinner {
            0% { -webkit-transform: rotate(0deg); }
            100% { -webkit-transform: rotate(360deg); }
        }
        
        @keyframes spinner {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="bg-gray-100 empleados-theme">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-gray-800 text-white">
            <div class="p-4">
                <h1 class="text-2xl font-bold mb-6">Sistema de Gestión</h1>
                <ul>
                    <li class="mb-3">
                        <button id="empleados-btn" class="flex items-center w-full py-2 px-4 rounded transition-colors bg-blue-600 hover:bg-blue-700 text-white">
                            <i class="fas fa-users mr-2"></i> Empleados
                        </button>
                    </li>
                    <li>
                        <button id="jefes-btn" class="flex items-center w-full py-2 px-4 rounded transition-colors bg-gray-700 hover:bg-green-700 text-white">
                            <i class="fas fa-user-tie mr-2"></i> Jefes
                        </button>
                    </li>
                </ul>
            </div>
            
            <div class="p-4 border-t border-gray-700">
                <h2 class="text-lg font-semibold mb-3">Operaciones</h2>
                <ul>
                    <li class="mb-2">
                        <button id="reportes-btn" class="flex items-center w-full py-2 px-4 rounded hover:bg-gray-700 transition-colors">
                            <i class="fas fa-chart-bar mr-2"></i> Reportes
                        </button>
                    </li>
                    <li class="mb-2">
                        <button id="auditorias-btn" class="flex items-center w-full py-2 px-4 rounded hover:bg-gray-700 transition-colors">
                            <i class="fas fa-clipboard-list mr-2"></i> Auditorías
                        </button>
                    </li>
                    <li>
                        <button id="procedimientos-btn" class="flex items-center w-full py-2 px-4 rounded hover:bg-gray-700 transition-colors">
                            <i class="fas fa-cogs mr-2"></i> Procedimientos
                        </button>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Navigation -->
            <header class="bg-white shadow-sm">
                <div class="flex justify-between items-center py-4 px-6">
                    <div class="flex items-center">
                        <h2 id="section-title" class="text-lg font-medium text-blue-600">Gestión de Empleados</h2>
                    </div>
                    <div class="flex items-center">
                        <div class="relative mr-4">
                            <input type="text" id="search-input" placeholder="Buscar..." class="py-2 pl-10 pr-4 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                        </div>
                        <button id="add-record-btn" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200 flex items-center">
                            <i class="fas fa-plus mr-2"></i> Agregar
                        </button>
                    </div>
                </div>
            </header>

            <!-- Filters Section -->
            <div class="bg-white border-b p-4">
                <div class="flex flex-wrap items-center gap-4">
                    <div>
                        <label for="filter-department" class="block text-sm font-medium text-gray-700 mb-1">Departamento</label>
                        <select id="filter-department" class="py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Todos</option>
                            <option value="Ventas">Ventas</option>
                            <option value="TI">TI</option>
                            <option value="Recursos Humanos">Recursos Humanos</option>
                            <option value="Finanzas">Finanzas</option>
                            <option value="Operaciones">Operaciones</option>
                            <option value="Marketing">Marketing</option>
                        </select>
                    </div>
                    <div>
                        <label for="filter-supervisor" class="block text-sm font-medium text-gray-700 mb-1">Supervisor</label>
                        <select id="filter-supervisor" class="py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Todos</option>
                            <option value="Sí">Sí</option>
                            <option value="No">No</option>
                        </select>
                    </div>
                    <div>
                        <label for="filter-date" class="block text-sm font-medium text-gray-700 mb-1">Fecha contratación</label>
                        <input type="date" id="filter-date" class="py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="sort-by" class="block text-sm font-medium text-gray-700 mb-1">Ordenar por</label>
                        <select id="sort-by" class="py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="nombres">Nombres</option>
                            <option value="apellidos">Apellidos</option>
                            <option value="departamento">Departamento</option>
                            <option value="fecha_contratacion">Fecha de contratación</option>
                            <option value="salario">Salario</option>
                        </select>
                    </div>
                    <div class="ml-auto">
                        <button id="clear-filters-btn" class="mt-6 px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Limpiar filtros
                        </button>
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-gray-500 text-sm font-medium">Total Empleados</h3>
                                <div class="mt-1">
                                    <div id="total-empleados" class="text-2xl font-semibold text-gray-900">0</div>
                                </div>
                            </div>
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-gray-500 text-sm font-medium">Promedio Salarial</h3>
                                <div class="mt-1">
                                    <div id="promedio-salario" class="text-2xl font-semibold text-gray-900">$0</div>
                                </div>
                            </div>
                            <div class="p-3 rounded-full bg-green-100 text-green-600">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-gray-500 text-sm font-medium">Supervisores</h3>
                                <div class="mt-1">
                                    <div id="total-supervisores" class="text-2xl font-semibold text-gray-900">0</div>
                                </div>
                            </div>
                            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                                <i class="fas fa-user-tie"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-gray-500 text-sm font-medium">Contratados este mes</h3>
                                <div class="mt-1">
                                    <div id="contratados-mes" class="text-2xl font-semibold text-gray-900">0</div>
                                </div>
                            </div>
                            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                                <i class="fas fa-calendar-plus"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table Section -->
                <div class="bg-white shadow rounded-lg mb-8">
                    <div class="px-6 py-5 border-b border-gray-200">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Lista de Empleados
                        </h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Departamento</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cargo</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Contratación</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supervisor</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Salario</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="empleados-table-body" class="bg-white divide-y divide-gray-200">
                                <?php 
                                if (empty($employees)) {
                                    echo '<tr><td colspan="9" class="px-6 py-4 text-center text-gray-500">No hay empleados registrados</td></tr>';
                                } else {
                                    foreach ($employees as $emp): 
                                ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($emp['id_empleado']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($emp['nombres'] . ' ' . $emp['apellidos']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($emp['correo_electronico']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($emp['departamento']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($emp['cargo']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo date('d/m/Y', strtotime($emp['fecha_contratacion'])); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $emp['supervisor'] === 'Sí' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                                            <?php echo htmlspecialchars($emp['supervisor']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">$<?php echo number_format($emp['salario'], 2); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button class="text-blue-600 hover:text-blue-900 mr-2" onclick="viewEmployeeDetails(<?php echo $emp['id']; ?>)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="text-indigo-600 hover:text-indigo-900 mr-2" onclick="editEmployee(<?php echo $emp['id']; ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="text-red-600 hover:text-red-900" onclick="deleteEmployee(<?php echo $emp['id']; ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php 
                                    endforeach;
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Add/Edit Employee Modal -->
    <div id="modal-empleado" class="modal opacity-0 pointer-events-none fixed w-full h-full top-0 left-0 flex items-center justify-center z-50">
        <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>
        
        <div class="modal-container bg-white w-11/12 md:max-w-3xl mx-auto rounded shadow-lg z-50 overflow-y-auto max-h-90vh">
            <div class="modal-content py-4 text-left px-6">
                <!-- Header -->
                <div class="flex justify-between items-center pb-3 border-b">
                    <p id="modal-title" class="text-xl font-bold">Agregar Empleado</p>
                    <div class="modal-close cursor-pointer z-50">
                        <i class="fas fa-times text-gray-500 hover:text-gray-800"></i>
                    </div>
                </div>
                
                <!-- Body -->
                <div class="mt-4">
                    <form id="empleado-form" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input type="hidden" id="empleado-id">
                        
                        <div class="col-span-1">
                            <label for="nombres" class="block text-sm font-medium text-gray-700">Nombres</label>
                            <input type="text" id="nombres" name="nombres" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        
                        <div class="col-span-1">
                            <label for="apellidos" class="block text-sm font-medium text-gray-700">Apellidos</label>
                            <input type="text" id="apellidos" name="apellidos" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        
                        <div class="col-span-1">
                            <label for="fecha_nacimiento" class="block text-sm font-medium text-gray-700">Fecha de Nacimiento</label>
                            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        
                        <div class="col-span-1">
                            <label for="correo_electronico" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                            <input type="email" id="correo_electronico" name="correo_electronico" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        
                        <div class="col-span-2">
                            <label for="direccion" class="block text-sm font-medium text-gray-700">Dirección</label>
                            <textarea id="direccion" name="direccion" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                        </div>
                        
                        <div class="col-span-1">
                            <label for="id_empleado" class="block text-sm font-medium text-gray-700">ID Empleado</label>
                            <input type="text" id="id_empleado" name="id_empleado" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        
                        <div class="col-span-1">
                            <label for="departamento" class="block text-sm font-medium text-gray-700">Departamento</label>
                            <select id="departamento" name="departamento" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Seleccionar...</option>
                                <option value="Ventas">Ventas</option>
                                <option value="TI">TI</option>
                                <option value="Recursos Humanos">Recursos Humanos</option>
                                <option value="Finanzas">Finanzas</option>
                                <option value="Operaciones">Operaciones</option>
                                <option value="Marketing">Marketing</option>
                            </select>
                        </div>
                        
                        <div class="col-span-1">
                            <label for="salario" class="block text-sm font-medium text-gray-700">Salario</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">$</span>
                                </div>
                                <input type="number" step="0.01" id="salario" name="salario" required class="pl-8 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>
                        
                        <div class="col-span-1">
                            <label for="genero" class="block text-sm font-medium text-gray-700">Género</label>
                            <select id="genero" name="genero" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Seleccionar...</option>
                                <option value="Masculino">Masculino</option>
                                <option value="Femenino">Femenino</option>
                                <option value="Otro">Otro</option>
                                <option value="Prefiero no decir">Prefiero no decir</option>
                            </select>
                        </div>
                        
                        <div class="col-span-1">
                            <label for="telefono" class="block text-sm font-medium text-gray-700">Teléfono</label>
                            <input type="tel" id="telefono" name="telefono" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        
                        <div class="col-span-1">
                            <label for="ciudad" class="block text-sm font-medium text-gray-700">Ciudad</label>
                            <input type="text" id="ciudad" name="ciudad" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        
                        <div class="col-span-1">
                            <label for="fecha_contratacion" class="block text-sm font-medium text-gray-700">Fecha de Contratación</label>
                            <input type="date" id="fecha_contratacion" name="fecha_contratacion" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        
                        <div class="col-span-1">
                            <label for="cargo" class="block text-sm font-medium text-gray-700">Cargo</label>
                            <input type="text" id="cargo" name="cargo" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        
                        <div class="col-span-1">
                            <label for="supervisor" class="block text-sm font-medium text-gray-700">Supervisor</label>
                            <select id="supervisor" name="supervisor" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Seleccionar...</option>
                                <option value="Sí">Sí</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                        
                        <div class="col-span-2">
                            <label for="notas_adicionales" class="block text-sm font-medium text-gray-700">Notas Adicionales</label>
                            <textarea id="notas_adicionales" name="notas_adicionales" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                        </div>
                    </form>
                </div>
                
                <!-- Footer -->
                <div class="flex justify-end pt-2 border-t mt-4">
                    <button class="modal-close px-4 bg-gray-200 p-3 rounded-lg text-gray-800 hover:bg-gray-300 mr-2">Cancelar</button>
                    <button id="save-empleado" class="px-4 bg-blue-500 p-3 rounded-lg text-white hover:bg-blue-600">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loading-overlay" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-5 rounded-lg flex items-center">
            <div class="loader ease-linear rounded-full border-4 border-t-4 border-gray-200 h-12 w-12 mr-4"></div>
            <p id="loading-message" class="text-gray-800 text-lg font-semibold">Cargando datos...</p>
        </div>
    </div>

    <script>
        // Current section/mode
        let currentSection = 'empleados';
        let currentPage = 1;
        let itemsPerPage = 10;
        
        // DOM ready
        $(document).ready(function() {
            // Update statistics
            updateStatistics();
            
            // Tab switching
            $('#empleados-btn').click(function() {
                switchToSection('empleados');
            });
            
            $('#jefes-btn').click(function() {
                switchToSection('jefes');
            });
            
            // Modal open/close events
            $('#add-record-btn').click(function() {
                openAddEmployeeModal();
            });
            
            $('.modal-close').click(function() {
                closeAllModals();
            });
            
            $('.modal-overlay').click(function() {
                closeAllModals();
            });
            
            // Save employee
            $('#save-empleado').click(function() {
                saveEmployee();
            });
            
            // Search functionality
            $('#search-input').on('keyup', function() {
                currentPage = 1;
                loadEmployees();
            });
            
            // Filters
            $('#filter-department, #filter-supervisor, #filter-date, #sort-by').change(function() {
                currentPage = 1;
                loadEmployees();
            });
            
            // Clear filters
            $('#clear-filters-btn').click(function() {
                $('#filter-department').val('');
                $('#filter-supervisor').val('');
                $('#filter-date').val('');
                $('#sort-by').val('nombres');
                $('#search-input').val('');
                currentPage = 1;
                loadEmployees();
            });
        });
        
        // Switch between sections (Empleados/Jefes)
        function switchToSection(section) {
            currentSection = section;
            currentPage = 1;
            
            if (section === 'empleados') {
                $('#empleados-btn').removeClass('bg-gray-700').addClass('bg-blue-600 hover:bg-blue-700');
                $('#jefes-btn').removeClass('bg-green-700').addClass('bg-gray-700 hover:bg-green-700');
                $('#section-title').text('Gestión de Empleados').removeClass('text-green-600').addClass('text-blue-600');
                $('#add-record-btn').removeClass('bg-green-600 hover:bg-green-700').addClass('bg-blue-600 hover:bg-blue-700');
                $('body').removeClass('jefes-theme').addClass('empleados-theme');
            } else {
                $('#jefes-btn').removeClass('bg-gray-700').addClass('bg-green-600 hover:bg-green-700');
                $('#empleados-btn').removeClass('bg-blue-600').addClass('bg-gray-700 hover:bg-blue-700');
                $('#section-title').text('Gestión de Jefes').removeClass('text-blue-600').addClass('text-green-600');
                $('#add-record-btn').removeClass('bg-blue-600 hover:bg-blue-700').addClass('bg-green-600 hover:bg-green-700');
                $('body').removeClass('empleados-theme').addClass('jefes-theme');
            }
            
            // Update the UI
            loadEmployees();
            updateStatistics();
        }
        
        // Update statistics cards
        function updateStatistics() {
            let filteredEmployees = getFilteredEmployees();
            
            // Total employees
            $('#total-empleados').text(filteredEmployees.length);
            
            // Average salary
            let totalSalary = filteredEmployees.reduce((sum, emp) => sum + parseFloat(emp.salario), 0);
            let avgSalary = filteredEmployees.length > 0 ? totalSalary / filteredEmployees.length : 0;
            $('#promedio-salario').text('$' + formatMoney(avgSalary));
            
            // Total supervisors
            let supervisors = filteredEmployees.filter(emp => emp.supervisor === 'Sí').length;
            $('#total-supervisores').text(supervisors);
            
            // Hired this month
            const now = new Date();
            const thisMonth = now.getMonth();
            const thisYear = now.getFullYear();
            
            let hiredThisMonth = filteredEmployees.filter(emp => {
                const hireDate = new Date(emp.fecha_contratacion);
                return hireDate.getMonth() === thisMonth && hireDate.getFullYear() === thisYear;
            }).length;
            
            $('#contratados-mes').text(hiredThisMonth);
        }
        
        // Open add employee modal
        function openAddEmployeeModal() {
            $('#modal-title').text('Agregar Empleado');
            $('#empleado-id').val('');
            $('#empleado-form')[0].reset();
            
            // Set default values
            const today = new Date().toISOString().split('T')[0];
            $('#fecha_contratacion').val(today);
            
            openModal('modal-empleado');
        }
        
        // View employee details
        function viewEmployeeDetails(id) {
            showLoading('Cargando detalles...');
            
            $.ajax({
                url: 'index2.php',
                type: 'POST',
                data: {
                    action: 'get',
                    id: id
                },
                success: function(response) {
                    if (response.success) {
                        const emp = response.data;
                        $('#detalle-nombre').text(`${emp.nombres} ${emp.apellidos}`);
                        $('#detalle-id-empleado').text(emp.id_empleado);
                        $('#detalle-email').text(emp.correo_electronico);
                        $('#detalle-telefono').text(emp.telefono || 'No disponible');
                        $('#detalle-departamento').text(emp.departamento);
                        $('#detalle-cargo').text(emp.cargo);
                        $('#detalle-fecha-nacimiento').text(formatDate(emp.fecha_nacimiento));
                        $('#detalle-fecha-contratacion').text(formatDate(emp.fecha_contratacion));
                        $('#detalle-salario').text('$' + formatMoney(emp.salario));
                        $('#detalle-supervisor').text(emp.supervisor);
                        $('#detalle-direccion').text(emp.direccion || 'No disponible');
                        $('#detalle-notas').text(emp.notas_adicionales || 'Sin notas adicionales');
                        
                        openModal('modal-detalles');
                    } else {
                        showToast('error', 'Error', response.message);
                    }
                },
                error: function() {
                    showToast('error', 'Error', 'Error al cargar los detalles del empleado');
                },
                complete: function() {
                    hideLoading();
                }
            });
        }
        
        // Modal utility functions
        function openModal(modalId) {
            $(`#${modalId}`).removeClass('opacity-0 pointer-events-none');
            $('body').addClass('modal-active');
        }
        
        function closeAllModals() {
            $('.modal').addClass('opacity-0 pointer-events-none');
            $('body').removeClass('modal-active');
        }
        
        // Loading overlay
        function showLoading(message = 'Cargando...') {
            $('#loading-message').text(message);
            $('#loading-overlay').removeClass('hidden');
        }
        
        function hideLoading() {
            $('#loading-overlay').addClass('hidden');
        }
        
        // Toast notifications
        function showToast(icon, title, message) {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
            
            Toast.fire({
                icon: icon,
                title: title,
                text: message
            });
        }
        
        // Helper functions
        function formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleDateString('es-MX');
        }
        
        function formatMoney(amount) {
            return parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        }
        
        // Load employees from database
        function loadEmployees() {
            showLoading('Cargando empleados...');
            
            $.ajax({
                url: 'index2.php',
                type: 'GET',
                success: function(response) {
                    // Parse the HTML response to get the table body
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(response, 'text/html');
                    const newTableBody = doc.getElementById('empleados-table-body').innerHTML;
                    
                    // Update the table body
                    $('#empleados-table-body').html(newTableBody);
                    
                    // Update statistics
                    updateStatistics();
                },
                error: function() {
                    showToast('error', 'Error', 'Error al cargar los empleados');
                },
                complete: function() {
                    hideLoading();
                }
            });
        }
        
        // Save employee function
        function saveEmployee() {
            showLoading('Guardando datos...');
            
            const id = $('#empleado-id').val();
            const formData = new FormData();
            formData.append('action', id ? 'update' : 'create');
            
            // Add all form fields to formData
            $('#empleado-form').serializeArray().forEach(item => {
                formData.append(item.name, item.value);
            });
            
            if (id) {
                formData.append('id', id);
            }
            
            $.ajax({
                url: 'index2.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        showToast('success', 'Éxito', response.message);
                        closeAllModals();
                        loadEmployees(); // Reload the table with updated data
                    } else {
                        showToast('error', 'Error', response.message);
                    }
                },
                error: function() {
                    showToast('error', 'Error', 'Error al guardar el empleado');
                },
                complete: function() {
                    hideLoading();
                }
            });
        }
        
        // Delete employee function
        function deleteEmployee(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción no se puede revertir",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    showLoading('Eliminando empleado...');
                    
                    $.ajax({
                        url: 'index2.php',
                        type: 'POST',
                        data: {
                            action: 'delete',
                            id: id
                        },
                        success: function(response) {
                            if (response.success) {
                                showToast('success', 'Eliminado', response.message);
                                loadEmployees(); // Reload the table with updated data
                            } else {
                                showToast('error', 'Error', response.message);
                            }
                        },
                        error: function() {
                            showToast('error', 'Error', 'Error al eliminar el empleado');
                        },
                        complete: function() {
                            hideLoading();
                        }
                    });
                }
            });
        }
        
        // Edit employee function
        function editEmployee(id) {
            showLoading('Cargando datos...');
            
            $.ajax({
                url: 'index2.php',
                type: 'POST',
                data: {
                    action: 'get',
                    id: id
                },
                success: function(response) {
                    if (response.success) {
                        const emp = response.data;
                        $('#modal-title').text('Editar Empleado');
                        $('#empleado-id').val(emp.id);
                        
                        // Fill form with employee data
                        $('#nombres').val(emp.nombres);
                        $('#apellidos').val(emp.apellidos);
                        $('#fecha_nacimiento').val(emp.fecha_nacimiento);
                        $('#correo_electronico').val(emp.correo_electronico);
                        $('#direccion').val(emp.direccion);
                        $('#id_empleado').val(emp.id_empleado);
                        $('#departamento').val(emp.departamento);
                        $('#salario').val(emp.salario);
                        $('#genero').val(emp.genero);
                        $('#telefono').val(emp.telefono);
                        $('#ciudad').val(emp.ciudad);
                        $('#fecha_contratacion').val(emp.fecha_contratacion);
                        $('#cargo').val(emp.cargo);
                        $('#supervisor').val(emp.supervisor);
                        $('#notas_adicionales').val(emp.notas_adicionales);
                        
                        openModal('modal-empleado');
                    } else {
                        showToast('error', 'Error', response.message);
                    }
                },
                error: function() {
                    showToast('error', 'Error', 'Error al cargar los datos del empleado');
                },
                complete: function() {
                    hideLoading();
                }
            });
        }
    </script>
</body>
</html>