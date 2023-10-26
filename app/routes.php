<?php

declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {

    // get
    $app->get('/user', function (Request $request, Response $response) {
        $db = $this->get(PDO::class);

        $query = $db->query('CALL lihatPengguna()');
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results));

        return $response->withHeader("Content-Type", "application/json");
    });

    // get by id
    $app->get('/user/{id}', function (Request $request, Response $response, $args) {
        $db = $this->get(PDO::class);

        $query = $db->prepare('CALL getUserById(:id)');
        $query->execute(['id' => $args['id']]);
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results[0]));

        return $response->withHeader("Content-Type", "application/json");
    });

   // post data
$app->post('/user', function (Request $request, Response $response) {
    $parsedBody = $request->getParsedBody();

    $nama = $parsedBody["nama"]; // Mengambil data dari body request
    $email = $parsedBody["email"];

    $db = $this->get(PDO::class);

    // Membuat panggilan ke stored procedure tambahPengguna
    $query = $db->prepare('CALL tambahPengguna(:nama, :email)');
    $query->bindParam(':nama', $nama, PDO::PARAM_STR);
    $query->bindParam(':email', $email, PDO::PARAM_STR);

    $query->execute();

    $response->getBody()->write(json_encode(
        [
            'message' => 'Pengguna disimpan'
        ]
    ));

    return $response->withHeader("Content-Type", "application/json");
}); 

  // put data
$app->put('/user/{id}', function (Request $request, Response $response, $args) {
    $parsedBody = $request->getParsedBody();

    $idPengguna = $args['id'];
    $nama = $parsedBody["nama"];
    $email = $parsedBody["email"];

    $db = $this->get(PDO::class);

    // Membuat panggilan ke stored procedure ubahPengguna
    $query = $db->prepare('CALL ubahPengguna(:idPengguna, :nama, :email)');
    $query->bindParam(':idPengguna', $idPengguna, PDO::PARAM_INT);
    $query->bindParam(':nama', $nama, PDO::PARAM_STR);
    $query->bindParam(':email', $email, PDO::PARAM_STR);

    $query->execute();

    $response->getBody()->write(json_encode(
        [
            'message' => 'Pengguna dengan ID ' . $idPengguna . ' telah diupdate'
        ]
    ));

    return $response->withHeader("Content-Type", "application/json");
});

//delete
$app->delete('/user/{id}', function (Request $request, Response $response, $args) {
    $currentId = $args['id'];
    $db = $this->get(PDO::class);

    try {
        // Membuat panggilan ke stored procedure hapusPengguna
        $query = $db->prepare('CALL hapusPengguna(:idPengguna)');
        $query->bindParam(':idPengguna', $currentId, PDO::PARAM_INT);
        $query->execute();

        if ($query->rowCount() === 0) {
            $response = $response->withStatus(404);
            $response->getBody()->write(json_encode(
                [
                    'message' => 'Data tidak ditemukan'
                ]
            ));
        } else {
            $response->getBody()->write(json_encode(
                [
                    'message' => 'Pengguna dengan ID ' . $currentId . ' telah dihapus dari database'
                ]
            ));
        }
    } catch (PDOException $e) {
        $response = $response->withStatus(500);
        $response->getBody()->write(json_encode(
            [
                'message' => 'Database error ' . $e->getMessage()
            ]
        ));
    }

    return $response->withHeader("Content-Type", "application/json");
});
    
// TABLE PUBLISHER
// get 
    $app->get('/publisher', function (Request $request, Response $response) {
        $db = $this->get(PDO::class);

        $query = $db->query('CALL lihatPublisher()');
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results));

        return $response->withHeader("Content-Type", "application/json");
    });

    // get publisher by ID
    $app->get('/publisher/{id}', function (Request $request, Response $response, $args) {
        $db = $this->get(PDO::class);

        $query = $db->prepare('CALL getPublisherById(:idPublisher)');
        $query->execute(['idPublisher' => $args['id']]);
        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $response->getBody()->write(json_encode($results[0]));

        return $response->withHeader("Content-Type", "application/json");
    });

    // CREATE
    $app->post('/publisher', function (Request $request, Response $response) {
        $parsedBody = $request->getParsedBody();

        $nama_publisher = $parsedBody["nama_publisher"];
        $kontak = $parsedBody["kontak"];

        $db = $this->get(PDO::class);

        $query = $db->prepare('CALL tambahPublisher(:nama_publisher, :kontak)');
        $query->bindParam(':nama_publisher', $nama_publisher, PDO::PARAM_STR);
        $query->bindParam(':kontak', $kontak, PDO::PARAM_STR);

        $query->execute();

        $response->getBody()->write(json_encode(
            [
                'message' => 'Penerbit disimpan'
            ]
        ));

        return $response->withHeader("Content-Type", "application/json");
    });

    // UPDATE
    $app->put('/publisher/{id}', function (Request $request, Response $response, $args) {
        $parsedBody = $request->getParsedBody();

        $idPublisher = $args['id'];
        $nama_publisher = $parsedBody["nama_publisher"];
        $kontak = $parsedBody["kontak"];

        $db = $this->get(PDO::class);

        $query = $db->prepare('CALL ubahPublisher(:idPublisher, :nama_publisher, :kontak)');
        $query->bindParam(':idPublisher', $idPublisher, PDO::PARAM_INT);
        $query->bindParam(':nama_publisher', $nama_publisher, PDO::PARAM_STR);
        $query->bindParam(':kontak', $kontak, PDO::PARAM_STR);

        $query->execute();

        $response->getBody()->write(json_encode(
            [
                'message' => 'Penerbit dengan ID ' . $idPublisher . ' telah diupdate'
            ]
        ));

        return $response->withHeader("Content-Type", "application/json");
    });

    // DELETE
    $app->delete('/publisher/{id}', function (Request $request, Response $response, $args) {
        $currentId = $args['id'];
        $db = $this->get(PDO::class);

        try {
            $query = $db->prepare('CALL hapusPublisher(:idPublisher)');
            $query->bindParam(':idPublisher', $currentId, PDO::PARAM_INT);
            $query->execute();

            if ($query->rowCount() === 0) {
                $response = $response->withStatus(404);
                $response->getBody()->write(json_encode(
                    [
                        'message' => 'Data tidak ditemukan'
                    ]
                ));
            } else {
                $response->getBody()->write(json_encode(
                    [
                        'message' => 'Penerbit dengan ID ' . $currentId . ' telah dihapus dari database'
                    ]
                ));
            }
        } catch (PDOException $e) {
            $response = $response->withStatus(500);
            $response->getBody()->write(json_encode(
                [
                    'message' => 'Database error ' . $e->getMessage()
                ]
            ));
        }

        return $response->withHeader("Content-Type", "application/json");
    });
};
