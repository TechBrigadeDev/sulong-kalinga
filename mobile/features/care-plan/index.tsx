import { Controller } from "common/api";
import { log } from "common/debug";

import { CarePlanFormData } from "./form/type";

class CarePlanController extends Controller {
    async postCarePlan(data: CarePlanFormData) {
        const response = await this.api.post(
            "/weekly-care-plans",
            data,
        );

        log(
            JSON.stringify(
                response.data,
                null,
                2,
            ),
            "CarePlanController.postCarePlan",
        );

        return response.data;
    }
}

export const carePlanController =
    new CarePlanController();
