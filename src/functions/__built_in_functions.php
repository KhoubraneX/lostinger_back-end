<?php
function uniqueID(string $prefix): string
{
    return substr(uniqid($prefix, true), 0, 15) . "_" . substr(rand(), 0, 5);
}

function imgToUrl($img): string
{
    // Remove the data URL prefix from the image data
    $imageData = preg_replace('#^data:image/[^;]+;base64,#', '', $img);

    // Decode the base64 image data
    $image = base64_decode($imageData);

    // Get information about the image
    $imageInfo = getimagesizefromstring($image);

    // Determine the extension of the image based on the MIME type
    // $mime = $imageInfo['mime'];
    $extension = image_type_to_extension($imageInfo[2]);

    // Generate a unique filename for the image
    $filename = uniqid() . $extension;
    $filenameSave = "../img/items/" . $filename;

    // Save the image to a file on the server
    file_put_contents($filenameSave, $image);

    return $filename;
}

function notFound(): void
{
    http_response_code(404);
    print_r(json_encode(["message" => "Not Found"]));
    exit;
}

function notAllodMethods(string $allowed_method)
{
    http_response_code("405");
    header("Allow: {$allowed_method}");
    exit;
}

function unprocessableContent(array $errors)
{
    http_response_code(422);
    echo json_encode(["message" => "The given data was invalid.", "errors" => $errors]);
    exit;
}

function validVal(array $arryError)
{
    unprocessableContent($arryError);
}

function checkAuth()
{
    $auth = new UserGateway;
    if ($auth->authAccessToken() === false) exit;
    return $auth->authAccessToken();
}


