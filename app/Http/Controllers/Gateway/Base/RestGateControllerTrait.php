<?php

namespace App\Http\Controllers\Gateway\Base;


use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;

trait RestGateControllerTrait
{

    protected function pagedFilterRequest(Request $request, $subUri = '')
    {
        try {

            $sort = explode('.', $request->get('sort'));
            $sortColumn = $sort[0] ?? '';
            $sortOrder = $sort[1] ?? '';
            $filter = $request->get('filter') ?? 'all';

            $parameters =
                array_merge(
                    $request->all(), [
                    'sort' => $sortColumn . '.' . $sortOrder,
                    'filter' => $filter,
                ]);

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

    public function index(Request $request)
    {
        Log::info('index');
        return $this->pagedFilterRequest($request);
    }

    protected function postOnly(Request $request, $subUrl = '')
    {
        try {
            return $this->service->postAsync(
                $this->targetUri . $subUrl,
                $request->input()
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

    public function show($id, Request $request)
    {
        try {
            return $this->service->getAsync(
                $this->targetUri . "/{id}", array_unique(
                array_merge(
                    $request->all(),
                    ['id' => $id]
                )))
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

    public function create()
    {
        try {
            return $this->service->getAsync($this->targetUri . "/create")
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

    public function store(Request $request)
    {
        try {
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

    public function edit($id)
    {
        try {
            return $this->service->getAsync($this->targetUri . "/{id}/edit", ['id' => $id])
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

    public function update($id, Request $request)
    {
        try {
            return $this->service->postAsync($this->targetUri . "/{id}", $request->input(), ['id' => $id], 'put')
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

    public function destroy($id)
    {
        try {
            return $this->service->destroyAsync($this->targetUri . "/{id}", [
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

    public function destroyBulk(Request $request = null)
    {
        try {
            $ids = $request->get('ids');

            if (!$ids) {
                throw \Exception::inst('Request ids not found.', Response::HTTP_BAD_REQUEST);
            }

            return $this->service->destroyAsync($this->targetUri, [
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

    public function markAs($id, $status)
    {
        try {
            return $this->service->postAsync($this->targetUri . "/{id}/mark_as/{status}", [], [
                'id' => $id,
                'status' => $status
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

    public function markAsBulk(Request $request, $status)
    {
        try {
            $ids = $request->get('ids');

            if (!$ids) {
                throw \Exception::inst('Request ids not found.', Response::HTTP_BAD_REQUEST);
            }

            return $this->service->postAsync($this->targetUri . "/mark_as/{status}", [], [
                'ids' => $ids,
                'status' => $status
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
