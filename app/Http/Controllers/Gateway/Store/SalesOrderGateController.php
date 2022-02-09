<?php

namespace App\Http\Controllers\Gateway\Store;

/**
 * @author Jehan Afwazi Ahmad <jee.archer@gmail.com>.
 */

use App\Exceptions\AppException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;

class SalesOrderGateController extends StoreGateController
{
    protected $targetUri = '/sales_orders';

    public function getItemsBySoId($soId)
    {
        try {
            return $this->service->getAsync($this->targetUri . "/{soId}/details", ['soId' => $soId])
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

    #INVOICE
    public function getInvoiceBySoId($soId)
    {
        try {
            return $this->service->getAsync($this->targetUri . "/{soId}/invoices", ['soId' => $soId])
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

    public function getInvoiceByIdAndSoId($soId, $invId)
    {
        try {
            return $this->service->getAsync(
                $this->targetUri . "/{soId}/invoices/{invId}", [
                'soId' => $soId, 'invId' => $invId
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

    public function generatePDFInvoiceByIdAndSoId($soId, $invId, Request $request)
    {

        try {
            $getUrl = $request->get('get_file_url') == 'true' ?: false;

            return $this->service->getAsync(
                $this->targetUri . "/{soId}/invoices/{invId}/pdf", [
                'soId' => $soId,
                'invId' => $invId,
                'get_file_url' => $getUrl
            ])->then(
                function (ResponseInterface $res) use ($getUrl) {

                    if ($getUrl)
                        return $this->jsonGzSuccess($res);

                    Log::info('generate pdf');
                    header('Access-Control-Allow-Origin: *');
                    header('Access-Control-Allow-Credential: true');
                    header('Access-Control-Allow-Methods: POST, PUT, DELETE, OPTIONS, HEAD, GET');
                    header('Access-Control-Allow-Headers: Content-type, Origin, Accept, Authorization, X-Header-Organization-Id');

                    //TODO(jee): hard header
                    header('Content-Disposition: inline; filename="' . str_random(10) . '.pdf"');
                    header('Transfer-Encoding: chunked');
                    header('Cache-Control: no-cache, no-store');
                    header('Content-Transfer-Encoding: binary');
                    header('Accept-Ranges: bytes');
                    header('Content-Type: application/pdf');

                    echo $res->getBody()->getContents();
                    exit;
                },
                function (RequestException $e) {
                    return $this->jsonExceptions($e);
                }
            )->wait();

        } catch (\Exception $e) {
            return $this->jsonExceptions($e);
        }
    }


    public function generateBulkPDF(Request $request)
    {

        try {

            $getUrl = $request->get('get_file_url') == 'true' ?: false;

            return $this->service->getAsync(
                $this->targetUri . "/invoices/bulk-pdf", [
                'ids' => $this->request->get('ids'),
                'get_file_url' => $getUrl
            ])->then(
                function (ResponseInterface $res) use ($getUrl) {

                    if ($getUrl)
                        return $this->jsonGzSuccess($res);

                    Log::info('generate pdf');

                    header('Access-Control-Allow-Origin: *');
                    header('Access-Control-Allow-Credential: true');
                    header('Access-Control-Allow-Methods: POST, PUT, DELETE, OPTIONS, HEAD, GET');
                    header('Access-Control-Allow-Headers: Content-type, Origin, Accept, Authorization, X-Header-Organization-Id');

                    header('Content-Disposition: inline; filename="' . str_random(10) . '.pdf"');
                    header('Transfer-Encoding: chunked');
                    header('Cache-Control: no-cache, no-store');
                    header('Content-Transfer-Encoding: binary');
                    header('Accept-Ranges: bytes');
                    header('Content-Type: application/pdf');

                    echo $res->getBody()->getContents();
                    exit;
                },
                function (RequestException $e) {
                    return $this->jsonExceptions($e);
                }
            )->wait();

        } catch (\Exception $e) {
            return $this->jsonExceptions($e);
        }
    }

    public function sendInvoiceEmailByIdAndSoIdInDetail($soId, $invId)
    {
        try {
            return $this->service->getAsync($this->targetUri . "/{soId}/invoices/{invId}/email", [
                'soId' => $soId, 'invId' => $invId
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

    public function sendInvoiceEmailByIdAndSoId($soId, $invId, Request $request)
    {
        try {
            return $this->service->postAsync($this->targetUri . "/{soId}/invoices/{invId}/email",
                $request->input(), [
                    'soId' => $soId,
                    'invId' => $invId
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

    public function markAsSent($soId, $invId)
    {
        try {
            return $this->service->getAsync(
                $this->targetUri . "/{soId}/invoices/{invId}/mark_as_sent", [
                'soId' => $soId,
                'invId' => $invId
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

    public function markAsSentPost($soId, $invId)
    {
        try {
            return $this->service->postAsync(
                $this->targetUri . "/{soId}/invoices/{invId}/mark_as_sent", [], [
                'soId' => $soId,
                'invId' => $invId
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

    public function markAsVoid($soId, $invId)
    {
        try {
            return $this->service->getAsync(
                $this->targetUri . "/{soId}/invoices/{invId}/mark_as_void", [
                'soId' => $soId,
                'invId' => $invId
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

    public function markAsVoidPost($soId, $invId)
    {
        try {
            return $this->service->postAsync(
                $this->targetUri . "/{soId}/invoices/{invId}/mark_as_void", [], [
                'soId' => $soId,
                'invId' => $invId
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

    #PAYMENT
    public function fetchPaymentByInvoiceId($soId, $invId)
    {
        try {
            return $this->service->getAsync(
                $this->targetUri . "/{soId}/invoices/{invId}/payments", [
                'soId' => $soId,
                'invId' => $invId
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

    public function getPaymentByIdAndInvoiceId($soId, $invId, $id)
    {
        try {
            return $this->service->getAsync($this->targetUri . "/{soId}/invoices/{invId}/payments/{id}", [
                'soId' => $soId,
                'invId' => $invId,
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

    public function createPayment($soId, $invId)
    {
        try {
            return $this->service->getAsync($this->targetUri . "/{soId}/invoices/{invId}/payments/create", [
                'soId' => $soId,
                'invId' => $invId
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

    public function storePayment($soId, $invId, Request $request)
    {
        try {
            return $this->service->postAsync($this->targetUri . "/{soId}/invoices/{invId}/payments",
                $request->input(), [
                    'soId' => $soId,
                    'invId' => $invId
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

    public function editPayment($soId, $invId, $id)
    {
        try {
            return $this->service->getAsync($this->targetUri . "/{soId}/invoices/{invId}/payments/{id}/edit", [
                'soId' => $soId,
                'invId' => $invId,
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

    public function updatePayment($soId, $invId, $id, Request $request)
    {
        try {
            return $this->service->postAsync($this->targetUri . "/{soId}/invoices/{invId}/payments/{id}/update",
                $request->input(), [
                    'soId' => $soId,
                    'invId' => $invId,
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

    public function destroyPayment($soId, $invId, Request $request)
    {
        try {

            $ids = $request->get('ids');

            if (!$ids) {
                throw AppException::inst('Request ids $this->getResponse()->getBody()ot found.', Response::HTTP_BAD_REQUEST);
            }

            return $this->service->destroyAsync($this->targetUri . "/{soId}/invoices/{invId}/payments", [
                'soId' => $soId,
                'invId' => $invId,
                'ids' => $ids
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

    #SHIPMENT
    public function fetchShipmentBySoId($soId)
    {
        try {
            return $this->service->getAsync($this->targetUri . "/{soId}/shipments", [
                'soId' => $soId
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


    public function createShipment($soId)
    {
        try {
            return $this->service->getAsync($this->targetUri . "/{soId}/shipments/create", [
                'soId' => $soId
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

    public function storeShipment($soId, Request $request)
    {
        try {
            return $this->service->postAsync($this->targetUri . "/{soId}/shipments",
                $request->input(), [
                    'soId' => $soId
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

    public function editShipment($soId, $shipId)
    {
        try {
            return $this->service->getAsync($this->targetUri . "/{soId}/shipments/{shipId}/edit", [
                'soId' => $soId, 'shipId' => $shipId
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

    public function updateShipment($soId, $shipId, Request $request)
    {
        try {
            return $this->service->postAsync($this->targetUri .
                "/{soId}/shipments/{shipId}/update",
                $request->input(), [
                    'soId' => $soId, 'shipId' => $shipId
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

    public function destroyShipment($soId, Request $request)
    {
        try {

            $ids = $request->get('ids');

            if (!$ids) {
                throw AppException::inst('Request ids not found.', Response::HTTP_BAD_REQUEST);
            }

            return $this->service->destroyAsync($this->targetUri . "/{soId}/shipments", [
                'soId' => $soId,
                'ids' => $ids
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

    public function updateDetail($soId, $detailId, Request $request)
    {
        try {
            return $this->service->postAsync($this->targetUri .
                "/{soId}/details/{detailId}",
                $request->input(), [
                    'soId' => $soId,
                    'detailId' => $detailId,
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

//    public function downloadShipmentLabel(Request $request)
//    {
//        try {
//
//            return $this->service->getAsync($this->targetUri . "/shipments/download-labels", [
//                    'ids' => $request->get('ids')
//                ]
//            )->then(
//                function (ResponseInterface $res) {
//
//                    Log::info('generate shipment label');
//
//                    header('Access-Control-Allow-Origin: *');
//                    header('Access-Control-Allow-Credential: true');
//                    header('Access-Control-Allow-Methods: POST, PUT, DELETE, OPTIONS, HEAD, GET');
//                    header('Access-Control-Allow-Headers: Content-type, Origin, Accept, Authorization, X-Header-Organization-Id');
//
//                    //TODO(jee): hard header
//                    header('Content-Disposition: inline; filename="' . str_random(10) . '.pdf"');
//                    header('Transfer-Encoding: chunked');
//                    header('Cache-Control: no-cache, no-store');
//                    header('Content-Transfer-Encoding: binary');
//                    header('Accept-Ranges: bytes');
//                    header('Content-Type: application/pdf');
//
//                    echo $res->getBody()->getContents();
//                    exit;
//                },
//                function (RequestException $e) {
//                    return $this->jsonExceptions($e);
//                }
//            )->wait();
//
//        } catch (\Exception $e) {
//            return $this->jsonExceptions($e);
//        }

    public function generateShipmentLabelBulkPDF(Request $request)
    {
        try {

            return $this->service->getAsync($this->targetUri . "/shipments/bulk-label", [
                    'ids' => $request->get('ids')
                ]
            )->then(
                function (ResponseInterface $res) {

                    Log::info('generate shipment label');

                    header('Access-Control-Allow-Origin: *');
                    header('Access-Control-Allow-Credential: true');
                    header('Access-Control-Allow-Methods: POST, PUT, DELETE, OPTIONS, HEAD, GET');
                    header('Access-Control-Allow-Headers: Content-type, Origin, Accept, Authorization, X-Header-Organization-Id');

                    //TODO(jee): hard header
                    header('Content-Disposition: inline; filename="' . str_random(10) . '.pdf"');
                    header('Transfer-Encoding: chunked');
                    header('Cache-Control: no-cache, no-store');
                    header('Content-Transfer-Encoding: binary');
                    header('Accept-Ranges: bytes');
                    header('Content-Type: application/pdf');

                    echo $res->getBody()->getContents();
                    exit;
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
