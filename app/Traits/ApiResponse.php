<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Exceptions\HttpResponseException;

trait ApiResponse
{
    public function responseWithMeta(
        $data = [],
        $meta,
        $status = true,
        $code = 200,
        $message = ''
    ) {
        return response()->json([
            'success' => $status,
            'message' => $message,
            'data' => $data,
            'meta' => $meta,
        ], $code);
    }

    /**
     * JSON response for APIs
     *
     * @param bool $status
     * @param string|array $message
     * @param array|object $data
     * @param int $code
     * @return Response
     */
    public function successResponse(
        $data = [],
        $message = '',
        $status = true,
        $code = 200,
    ) {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    public function badRequestResponse($message = 'Your Request Is Invalid')
    {
        return $this->errorResponse($message, Response::HTTP_BAD_REQUEST);
    }
    public function createdResponse($message = 'Your request done successfully')
    {
        return response()->json([
            'status' => true,
            'message' => $message,
        ], Response::HTTP_CREATED);
    }
    public function notFoundResponse($message = 'Not Found')
    {
        return $this->errorResponse($message, Response::HTTP_NOT_FOUND);
    }
    public function abortNotAllowed($msg)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => $msg,
        ], 403));
    }
    public function abortNotFound($msg)
    {
        throw new HttpResponseException($this->notFoundResponse($msg));
    }
    public function noContentResponse()
    {
        return response()->json([
            'status' => true,
            'message' => 'Your request done successfully',
        ], Response::HTTP_NO_CONTENT);
    }

    public function unauthorizedResponse($message = 'Unauthorized')
    {
        return $this->errorResponse($message, Response::HTTP_UNAUTHORIZED);
    }

    public function errorResponse(
        $message = 'Your Request Is Invalid',
        $code = JsonResponse::HTTP_BAD_REQUEST,
        $data = []
    ) {
        return response()->json([
            'status' => false,
            'message' => $message,
            "errors" => $data
        ], $code);
    }
    public function validationResponse(array $errors = [])
    {
        return response()->json([
            'status' => false,
            'message' => 'Your Request Is Invalid',
            "errors" => $errors
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
