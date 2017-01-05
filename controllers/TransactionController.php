<?php

namespace komer45\balance\controllers;

use Yii;
use komer45\balance\models\Transaction;
use komer45\balance\models\SearchTransaction;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TransactionController implements the CRUD actions for Transaction model.
 */
class TransactionController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Transaction models.
     * @return mixed
     */
    public function actionIndex($id = null)
    {
        $searchModel = new SearchTransaction();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		
		if($id){
			$dataProvider->query->andWhere(['user_id' => $id]);
			$dataProvider->sort->defaultOrder = ['id' => SORT_DESC];	
		}
		
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Transaction model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Transaction model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Transaction();
		
        if ($model->load(Yii::$app->request->post())) {
			$addTransaction = Yii::$app->balance->addTransaction($model->balance_id, $model->type, $model->amount, $model->refill_type);
			return $this->redirect(['view', 'id' => $addTransaction]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Transaction model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Transaction model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
	 
    /*public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }*/

    /**
     * Finds the Transaction model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Transaction the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Transaction::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
	
	public function actionTransactionInvert($id)
	{
		$newTransaction = new Transaction;
		
		$transaction = Transaction::findOne($id);
		$transaction->canceled = date('Y.m.d H:m:s');
		
		if ($transaction->type == 'in'){		//операция "приход"
			$newTransaction->type = 'out';
		}else {
			$newTransaction->type = 'in';
		}
		
		$newTransaction->balance_id = $transaction->balance_id;
		$newTransaction->date =	date('Y.m.d H:m:s');
		$newTransaction->amount = $transaction->amount;
		$newTransaction->balance = $transaction->balance;
		$newTransaction->user_id = $transaction->user_id;
		$newTransaction->refill_type = $transaction->refill_type;
		if($newTransaction->validate()){
			$newTransaction->save();
		} else {
			return die("Uh-ho, somethings in 'TransactionController' went wrong!");
		}
		$transaction->update();
		return $this->redirect(['index']);
	}
}
