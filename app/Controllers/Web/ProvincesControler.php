// <?php
//
// namespace App\Controllers;
//
// use App\Models\Province;
// use JosueIsOffline\Framework\Controllers\AbstractController;
//
// class ProvinceController extends AbstractController
// {
//   public function createForm(): void
//   {
//     include __DIR__ . '/../Views/province_create.php';
//   }
//
//   public function store(): void
//   {
//     if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//       $province = new Province();
//       $province->create([
//         'name' => $_POST['name'],
//         'code' => $_POST['code']
//       ]);
//     }
//
//     header('Location: /provinces');
//     exit;
//   }
//
//
//   // Mostrar formulario para editar provincia
//   public function editForm(): void
//   {
//     $id = $_GET['id'] ?? null;
//
//     if (!$id || !is_numeric($id)) {
//       http_response_code(400);
//       echo "ID inválido";
//       exit;
//     }
//
//     $province = Province::find((int) $id);
//
//     if (!$province) {
//       http_response_code(404);
//       echo "Provincia no encontrada";
//       exit;
//     }
//
//     include __DIR__ . '/../Views/provinces/edit.php';
//   }
//
//
//   // Actualizar provincia
//   public function update(): void
//   {
//     if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//       if (!isset($_POST['id'], $_POST['name'])) {
//         http_response_code(400);
//         echo "Datos incompletos";
//         exit;
//       }
//
//       $province = new Province();
//       $province->update([
//         'id' => (int) $_POST['id'],
//         'name' => trim($_POST['name']),
//         'code' => trim($_POST['code'] ?? '')
//       ]);
//     }
//
//     header('Location: /provinces');
//     exit;
//   }
//
//
//   // Eliminar provincia
//   public function destroy(): void
//   {
//     if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//       $province = new Province();
//       $province->delete($_POST['id']);
//     }
//
//     header('Location: /provinces');
//     exit;
//   }
//
//   // Listar todas las provincias
//   public function index(): void
//   {
//     $provinces = Province::all();
//     include __DIR__ . '/../Views/province_index.php';
//   }
// }
