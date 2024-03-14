<?php

namespace App\Http\Controllers;

use App\Http\Requests\HolidayPlanRequest;
use App\Models\HolidayPlan;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Response;

/**
 * @OA\Info(
 *     title="Vacation Plan API",
 *     version="1.0.0",
 *     description="API for managing holiday plans.",
 *     @OA\Contact(
 *         email="pedrog_silva@outlook.com",
 *         name="Pedro Gabriel da Silva"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * ),
 * @OA\SecurityScheme(
 *     type="http",
 *     scheme="bearer",
 *     securityScheme="bearerAuth",
 *     bearerFormat="Bearer {token}"
 * )
 */
class HolidayPlanController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/holiday-plans",
     *     operationId="getAllHolidays",
     *     tags={"Holiday Plans"},
     *     summary="Get all holidays",
     *     description="Returns a list of all holidays.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="date", type="string", format="date"),
     *                 @OA\Property(property="location", type="string"),
     *                 @OA\Property(property="participants", type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="name", type="string")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="No content",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="No records"
     *             )
     *         )
     *     )
     * )
     */
    public function getAll()
    {
        $holidays = HolidayPlan::all();

        return $holidays->isEmpty()
            ? response()->json(['message' => 'No records'], 204)
            : response()->json($holidays, 200);
    }

    /**
     * @OA\Get(
     *     path="/api/holiday-plan/{id}",
     *     operationId="getHolidayById",
     *     tags={"Holiday Plans"},
     *     summary="Get holiday by ID",
     *     description="Returns a single holiday based on ID.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the holiday",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="date", type="string", format="date"),
     *             @OA\Property(property="location", type="string"),
     *             @OA\Property(property="participants", type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="name", type="string")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Holiday not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Record not found"
     *             )
     *         )
     *     )
     * )
     */
    public function getById($id)
    {
        $holiday = HolidayPlan::find($id);

        if (!$holiday) {
            return response()->json(['message' => 'Record not found.'], 404);
        }

        return response()->json($holiday, 200);
    }


    /**
     * @OA\Post(
     *     path="/api/holiday-plan",
     *     operationId="storeHolidayPlan",
     *     tags={"Holiday Plans"},
     *     summary="Create a new holiday plan",
     *     description="Create a new holiday plan.",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Holiday plan data",
     *         @OA\JsonContent(
     *             required={"title", "description", "date", "location", "participants"},
     *             @OA\Property(property="title", type="string", example="Summer Vacation"),
     *             @OA\Property(property="description", type="string", example="Let's go to the most famous resort in Brazil"),
     *             @OA\Property(property="date", type="string", format="date", example="2024-12-12"),
     *             @OA\Property(property="location", type="string", example="Fernando de Noronha"),
     *             @OA\Property(property="participants", type="array",
     *                 @OA\Items(type="string", example="Pedro"),
     *                 @OA\Items(type="string", example="Maria")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Holiday plan created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Plan created successfully!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation error")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Failed to register plan. ERROR: {error_message}")
     *         )
     *     )
     * )
     */
    public function store(HolidayPlanRequest $request)
    {
        DB::beginTransaction();
        try {

            $data = $request->all();

            $participants = [];
            if (isset($data['participants']) && is_array($data['participants'])) {
                foreach ($data['participants'] as $participant) {
                    $participants[] = ['name' => $participant];
                }
            }

            $data['participants'] = json_encode($participants);

            HolidayPlan::create($data);

            DB::commit();

            return response()->json(['message' => 'Holiday plan created successfully!'], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => "Failed to register plan. ERROR: {$th->getMessage()}"], 500);
        }
    }


    /**
     * @OA\Put(
     *     path="/api/holiday-plan/{id}",
     *     operationId="updateHolidayPlan",
     *     tags={"Holiday Plans"},
     *     summary="Update a holiday plan",
     *     description="Update an existing holiday plan.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the holiday plan to update",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Holiday plan data",
     *         @OA\JsonContent(
     *             required={"title", "description", "date", "location", "participants"},
     *             @OA\Property(property="title", type="string", example="Summer Vacation"),
     *             @OA\Property(property="description", type="string", example="Let's go to the most famous resort in Brazil"),
     *             @OA\Property(property="date", type="string", format="date", example="2024-12-12"),
     *             @OA\Property(property="location", type="string", example="Fernando de Noronha"),
     *             @OA\Property(property="participants", type="array",
     *                 @OA\Items(type="string", example="Pedro"),
     *                 @OA\Items(type="string", example="Bianca")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Holiday plan updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Holiday plan updated successfully!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation error")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Record not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Failed to update holiday plan. ERROR: {error_message}")
     *         )
     *     )
     * )
     */
    public function update(HolidayPlanRequest $request, $id)

    {
        DB::beginTransaction();

        try {

            $holiday = HolidayPlan::findOrFail($id);

            $data = $request->validated();

            $participants = [];
            if (isset($data['participants']) && is_array($data['participants'])) {
                foreach ($data['participants'] as $participant) {
                    $participants[] = ['name' => $participant];
                }
            }
            $data['participants'] = json_encode($participants);

            $holiday->update($data);

            DB::commit();

            return response()->json(['message' => 'Holiday plan updated successfully!'], 200);
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json(['message' => 'Record not found.'], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to update holiday plan. ERROR: ' . $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/holiday-plan/{id}",
     *     operationId="deleteHolidayPlan",
     *     tags={"Holiday Plans"},
     *     summary="Delete a holiday plan",
     *     description="Delete an existing holiday plan.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the holiday plan to delete",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Holiday plan deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Holiday plan removed successfully!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Record not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Failed to delete holiday plan. ERROR: {error_message}")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $holiday = HolidayPlan::find($id);

        if (!$holiday) {
            return response()->json(['message' => 'Record not found.'], 404);
        }

        $holiday->delete();

        DB::commit();

        return response()->json(['message' => 'Holiday plan removed successfully!'], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/holiday-plan/{id}/pdf",
     *     operationId="generateHolidayPlanPDF",
     *     tags={"Holiday Plans"},
     *     summary="Generate PDF for a holiday plan",
     *     description="Generate a PDF document for a specific holiday plan.",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the holiday plan",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="PDF generated successfully",
     *         @OA\MediaType(
     *             mediaType="application/pdf"
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Record not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function pdfGenerate($id)
    {
        $holiday = HolidayPlan::find($id);

        if (!$holiday) {
            return response()->json(['message' => 'Record not found.'], 404);
        }

        $date = Carbon::parse($holiday->date);

        $formattedDate = $date->format('d/m/Y');


        $html = '<h1>' . $holiday->title . '</h1>';
        $html .= '<p><strong>Description:</strong> ' . $holiday->description . '</p>';
        $html .= '<p><strong>Date:</strong> ' . $formattedDate . '</p>';
        $html .= '<p><strong>Location:</strong> ' . $holiday->location . '</p>';

        if ($holiday->participants) {
            $participants = json_decode($holiday->participants);
            $html .= '<p><strong>Participants:</strong></p>';
            $html .= '<ul>';
            foreach ($participants as $participant) {
                $html .= '<li>' . $participant->name . '</li>';
            }
            $html .= '</ul>';
        }

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $dompdf = new Dompdf($options);

        $dompdf->loadHtml($html);

        $dompdf->render();

        $output = $dompdf->output();

        // Definir cabeçalhos para fazer o navegador baixar o PDF
        $response = Response::make($output, 200);
        $response->header('Content-Type', 'application/pdf');
        $response->header('Content-Disposition', 'attachment; filename=holiday_plan.pdf');

        return $response;
    }
}
