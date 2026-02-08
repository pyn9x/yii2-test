<?php
namespace app\models\search;

use app\models\Author;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class AuthorSearch extends Author
{
    public function rules(): array
    {
        return [
            [['id'], 'integer'],
            [['name', 'bio'], 'safe'],
        ];
    }

    public function scenarios(): array
    {
        return Model::scenarios();
    }

    /**
     * @param array<string, mixed> $params
     */
    public function search(array $params): ActiveDataProvider
    {
        $query = Author::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 10],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['id' => $this->id]);
        $query->andFilterWhere(['like', 'name', $this->name]);
        $query->andFilterWhere(['like', 'bio', $this->bio]);

        return $dataProvider;
    }
}
