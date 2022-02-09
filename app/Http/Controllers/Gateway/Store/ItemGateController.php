<?php

namespace App\Http\Controllers\Gateway\Store;

use App\Cores\Image;
use App\Exceptions\AppException;
use App\Http\Requests\Gateway\Stores\Items\AddImageItemRequest;
use App\Http\Requests\Gateway\Stores\Items\ItemStoreRequest;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Psr\Http\Message\ResponseInterface;

/**
 * @author Jehan Afwazi Ahmad <jee.archer@gmail.com>.
 */
class ItemGateController extends StoreGateController
{
    protected $targetUri = '/items';

    protected function pagedFilterRequest(Request $request, $subUri = '')
    {
        try {

            $sort = explode('.', $request->input('sort'));
            $sortColumn = $sort[0] ?? '';
            $sortOrder = $sort[1] ?? '';
            $filter = $request->get('filter') ?? 'all';

            $parameters = array_unique(array_merge($request->all(), [
                'sort' => $sortColumn . '.' . $sortOrder,
                'filter' => $filter,
            ]));

            return $this->service->getAsync($this->targetUri . $subUri, $parameters)
                ->then(
                    function (ResponseInterface $res) {
                        return $this->jsonGzSuccess($res);
                    },
                    function (RequestException $e) {
                        return $this->jsonExceptions($e);
                    }

                )->wait();

        } catch (\Exception $e) {
            return $this->jsonExceptions($e);
        }
    }

    public function addAttribute($id, Request $request)
    {
        try {

            return $this->service->postAsync($this->targetUri . "/{id}/attributes/add",
                $request->input(), [
                    'id' => $id
                ])->then(
                function (ResponseInterface $res) {
                    return $this->jsonGzSuccess($res);
                },
                function (RequestException $e) {
                    return $this->jsonExceptions($e);
                }

            )->wait();

        } catch (\Exception $e) {
            return $this->jsonExceptions($e);
        }
    }

    public function updateAttributeKey($id, Request $request)
    {
        try {

            return $this->service->postAsync($this->targetUri . "/{id}/attributes/update",
                $request->input(), [
                    'id' => $id
                ])->then(
                function (ResponseInterface $res) {
                    return $this->jsonGzSuccess($res);
                },
                function (RequestException $e) {
                    return $this->jsonExceptions($e);
                }

            )->wait();

        } catch (\Exception $e) {
            return $this->jsonExceptions($e);
        }
    }

    public function destroyAttributeVal($id, Request $request)
    {
        try {

            return $this->service->postAsync($this->targetUri . "/{id}/attributes/delete",
                $request->input(), [
                    'id' => $id
                ])->then(
                function (ResponseInterface $res) {
                    return $this->jsonGzSuccess($res);
                },
                function (RequestException $e) {
                    return $this->jsonExceptions($e);
                }

            )->wait();

        } catch (\Exception $e) {
            return $this->jsonExceptions($e);
        }
    }

    public function updatePrice($id, Request $request)
    {
        try {

            return $this->service->postAsync($this->targetUri . "/{id}/update_price",
                $request->input(), [
                    'id' => $id
                ])->then(
                function (ResponseInterface $res) {
                    return $this->jsonGzSuccess($res);
                },
                function (RequestException $e) {
                    return $this->jsonExceptions($e);
                }

            )->wait();

        } catch (\Exception $e) {
            return $this->jsonExceptions($e);
        }
    }

    public function updateInventoryStock($id, Request $request)
    {
        try {

            return $this->service->postAsync($this->targetUri . "/{id}/update_inventory_stock",
                $request->input(), [
                    'id' => $id
                ])->then(
                function (ResponseInterface $res) {
                    return $this->jsonGzSuccess($res);
                },
                function (RequestException $e) {
                    return $this->jsonExceptions($e);
                }

            )->wait();

        } catch (\Exception $e) {
            return $this->jsonExceptions($e);
        }
    }

    public function addImage($id, Request $request)
    {
        try {

            return $this->service->postAsync($this->targetUri . "/{id}/images/add",
                $request->input(), [
                    'id' => $id,
                ])->then(
                function (ResponseInterface $res) {
                    return $this->jsonGzSuccess($res);
                },
                function (RequestException $e) {
                    return $this->jsonExceptions($e);
                }

            )->wait();

        } catch (\Exception $e) {
            return $this->jsonExceptions($e);
        }
    }

    public function setPrimary($id, $imgId)
    {
        try {

            return $this->service->getAsync($this->targetUri . "/{id}/images/set_primary/{imgId}", [
                'id' => $id,
                'imgId' => $imgId
            ])->then(
                function (ResponseInterface $res) {
                    return $this->jsonGzSuccess($res);
                },
                function (RequestException $e) {
                    return $this->jsonExceptions($e);
                }

            )->wait();

        } catch (\Exception $e) {
            return $this->jsonExceptions($e);
        }
    }

    public function removeImage($id, $imgId)
    {
        try {

            return $this->service->destroyAsync($this->targetUri . "/{id}/images/remove/{imgId}", [
                'id' => $id,
                'imgId' => $imgId
            ])->then(
                function (ResponseInterface $res) {
                    return $this->jsonGzSuccess($res);
                },
                function (RequestException $e) {
                    return $this->jsonExceptions($e);
                }

            )->wait();

        } catch (\Exception $e) {
            return $this->jsonExceptions($e);
        }
    }

    public function getUploadCredential()
    {
        try {

            return $this->service->getAsync($this->targetUri . "/get_upload_credential", [])->then(
                function (ResponseInterface $res) {
                    return $this->jsonGzSuccess($res);
                },
                function (RequestException $e) {
                    return $this->jsonExceptions($e);
                }

            )->wait();

        } catch (\Exception $e) {
            return $this->jsonExceptions($e);
        }
    }

    public function importMass()
    {
        try {

            $file = $this->request->file('raw_file');

            if (is_null($file))
                throw AppException::inst("FIle not found.");

            $fileUrl = $file
                ->storeAs(
                    Image::generatePath(
                        $this->request->header('X-Header-Organization-Id'),
                        'import_item_files'
                    ),
                    $file->getClientOriginalName(),
                    's3'
                );

            $this->request->request->add(['raw_file' => $fileUrl]);

            return $this->service->postAsync(
                $this->targetUri . "/import-mass",
                $this->request->input()
            )->then(
                function (ResponseInterface $res) {
                    return $this->jsonGzSuccess($res);
                },
                function (RequestException $e) {
                    return $this->jsonExceptions($e);
                }
            )->wait();

        } catch (\Exception $e) {
            return $this->jsonExceptions($e);
        }
    }

    //v2

    /**
     * @param AddImageItemRequest $request
     * @return string
     * @throws \Exception
     */
    public function uploadItemImageToS3(AddImageItemRequest $request)
    {
        try {
            $img = $request->file('image');

            if ($img instanceof UploadedFile) {

                $orgId = $request->header('X-Header-Organization-Id');
                $pathFormat = "temp/"
                    . sha1($orgId)
                    . '/items/'
                    . uniqid($orgId);
                $pathFile = $img->store($pathFormat, 's3');
                return $this->json(
                    Response::HTTP_CREATED,
                    "Upload image is successfully",
                    ['image_url' => env('S3_URL') . '/' . $pathFile]
                );
            }

            throw AppException::bad("Invalid image request type.");
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function storeV2(ItemStoreRequest $request)
    {
        try {

            $imagesRequest = $request->get('images');

            if (count($imagesRequest) > 5)
                throw AppException::bad('The could not upload image more than 5 items');

            return $this->service->postAsync($this->targetUri, $request->input())
                ->then(
                    function (ResponseInterface $res) {
                        return $this->jsonGzSuccess($res);
                    },
                    function (RequestException $e) {
                        return $this->jsonExceptions($e);
                    }
                )->wait();
        } catch (\Exception $e) {
            return $this->jsonExceptions($e);
        }
    }

    public function addImageV2($id, AddImageItemRequest $request)
    {
        try {

            $orgId = $request->header('X-Header-Organization-Id');
            $pathFormat = "temp/"
                . sha1($orgId)
                . '/items/'
                . uniqid($orgId);

            $imagePath = $request->file('image')->store($pathFormat, 's3');
            $imageUrl = env('S3_URL') . '/' . $imagePath;
            $request->merge(['data' => $imageUrl]);

            return $this->service->postAsync($this->targetUri . "/{id}/images/add",
                $request->input(), [
                    'id' => $id,
                ])->then(
                function (ResponseInterface $res) {
                    return $this->jsonGzSuccess($res);
                },
                function (RequestException $e) {
                    return $this->jsonExceptions($e);
                }

            )->wait();

        } catch (\Exception $e) {
            return $this->jsonExceptions($e);
        }
    }
}